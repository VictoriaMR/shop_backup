<?php

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 翻译接口类
 */
class TranslateService extends BaseService
{
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

	public function getText($name, $trCode)
    {
        if (empty($name)) return '';
        $cacheKey = 'SITE_TRANSLATE_TEXT_'.strtoupper($trCode);
        //获取缓存中对应的翻译文本
    	$info = redis(1)->hget($cacheKey, $name);
    	if (empty($info)) {
            //检查文本
            $this->setNotExist($name, $trCode);
            $info = $name;	
    	}
    	return $info;
    }

    protected function setNotExist($name, $trCode, $value='')
    {
        if ($this->isExistName($name, $trCode)) return true;
        $data = [
            'name' => $name,
            'tr_code' => $trCode, 
        ];
        if (!empty($value)) {
            $data['value'] = trim($value);
        }
        return make('App/Models/Translate')->insert($data);
    }

    protected function isExistName($name, $trCode)
    {
        return make('App/Models/Translate')->where('name', $name)->where('tr_code', $trCode)->count() > 0;
    }
}