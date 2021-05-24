<?php

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\Translate;

/**
 * 翻译接口类
 */
class TranslateService extends BaseService
{
    const CACHE_KEY = 'SITE_TRANSLATE_TEXT_';

	public function __construct(Translate $model)
	{
		$this->baseModel = $model;
	}

	public function getTranslate($text, $to = 'en', $from = 'zh')
	{
		if (empty(env('BAIDU_APPID')) || empty(env('BAIDU_SECRET_KEY'))) {
			return false;
		}
		if ($to == $from) {
			return $text;
		}
		$salt = time();
		$data = [
			'q' => $text,
			'from' => $from,
			'to' => $to,
			'appid' => env('BAIDU_APPID'),
			'salt' => $salt,
			'sign' => md5(env('BAIDU_APPID').$text.$salt.env('BAIDU_SECRET_KEY')),
		];
		$http_url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
		$request = $http_url.'?'.http_build_query($data);
		for ($i = 0; $i < 5; $i ++) {
			$translateStr = \frame\Http::get($request);
			if ($translateStr !== false) {
				$translateStr = json_decode($translateStr, true);
				if (!empty($translateStr['trans_result'][0]['dst'])) {
					return trim($translateStr['trans_result'][0]['dst']);
				}
			}
		}
		return '';
	}

	public function getText($name)
    {
        if (empty($name)) return '';
    	$code = \frame\Session::get('site_language_name');
        $cacheKey = 'SITE_TRANSLATE_TEXT_'.strtoupper($code);
        //获取缓存中对应的翻译文本
    	$info = redis(1)->hget($cacheKey, $name);
    	if (empty($info)) {
            //检查文本
            $value = $this->setNotExist($name, $code);
            if (utf8len($value) > 1) {
            	redis(1)->hset($cacheKey, $name, $value);
            	return $value;
            }
            return $name;
    	}
    	return $info;
    }

    protected function setNotExist($name, $code, $value='')
    {
    	$value = $this->isExistName($name, $code);
        if (!empty($value)) {
        	return $value;
        }
        $data = [
            'name' => $name,
            'type' => $code, 
        ];
        if (!empty($value)) {
            $data['value'] = trim($value);
        }
        return $this->baseModel->insert($data);
    }

    protected function isExistName($name, $code)
    {
        return $this->baseModel->where(['name'=>$name, 'type'=>$code])->value('value');
    }

    public function reloadCache()
    {
        $list = make('App/Services/LanguageService')->getInfo();
        foreach ($list as $key => $value) {
            $cacheKey = self::CACHE_KEY.strtoupper($value['code']);
            redis(1)->del($cacheKey);
        }

        $list = $this->baseModel->where('value', '<>', '')->get();

        $tempData = [];
        foreach ($list as $key => $value) {
            if (!isset($tempData[$value['type']])) {
                $tempData[$value['type']] = [];
            }
            $tempData[$value['type']][$value['name']] = $value['value'];
        }
        foreach ($tempData as $key => $value) {
            $cacheKey = self::CACHE_KEY.strtoupper($key);
            redis(1)->hmset($cacheKey, $value);
        }
        return true;
    }
}