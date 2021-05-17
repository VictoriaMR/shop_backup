<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	属性类
 */
class AttributeService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRIBUTE_CACHE';

	public function addNotExist($nameZh)
	{
		if (empty($nameZh)) {
			return false;
		}
        $nameZh = trim($nameZh);
        $translateService = make('App\Services\TranslateService');
        $nameEn = $translateService->getTranslate($nameZh);
        if (empty($nameEn)) $nameEn = $nameZh;
        $nameEn = ucfirst($nameEn);
        $info = $this->getInfoByName($nameEn);
        if (!empty($info)) {
            $info['name_zh'] = $nameZh;
            return $info;
        }
        $data = [
            'name' => $nameEn,
            'sort' => 0,
        ];
        $attrId = make('App\Models\Attribute')->create($data);
        //设置多语言
        $attrLanModel = make('App\Models\AttributeLanguage');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['code'] == 'en') continue;
            if ($value['code'] != 'zh') {
                $name = $nameZh;
            } else {
                $name = $translateService->getTranslate($nameZh, $value['code']);
            }
            $insert = [
            	'attr_id' => $attrId,
            	'lan_id' => $value['lan_id'],
            	'name' => $name,
            ];
            $attrLanModel->create($insert);
        }
        return true;
	}

    public function getInfoByName($name)
    {
        return make('App\Models\Attribute')->getInfoByWhere(['name' => $name]);
    }

    public function getInfo($attrId=null, $lanId=null)
    {
        if (empty($attrId)) {
            return false;
        }
        if (!is_array($attrId)) {
            $attrId = [$attrId];
        }
        $info = make('App\Models\Attribute')->whereIn('attr_id', $attrId)->field('attr_id, name')->get();
        if ($lanId > 0 && $lanId != env('DEFAULT_LANGUAGE_ID')) {
            $tempData = make('App\Models\AttributeLanguage')->whereIn('attr_id', $attrId)->where('lan_id', $lanId)->field('attr_id, name')->get();
            $tempData = array_column($tempData, 'name', 'attr_id');
            foreach ($info as $key => $value) {
                $info[$key]['name'] = $tempData[$value['attr_id']];
            }
        }
        return $info;
    }
}