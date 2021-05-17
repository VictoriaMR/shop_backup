<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	属性值类
 */
class AttrvalueService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRVALUE_CACHE';

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
        $attvId = make('App\Models\Attrvalue')->create($data);
        //设置多语言
        $attrLanModel = make('App\Models\AttrvalueLanguage');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['code'] == 'en') continue;
            if ($value['code'] != 'zh') {
                $name = $nameZh;
            } else {
                $name = $translateService->getTranslate($nameZh, $value['code']);
            }
            $insert = [
                'attv_id' => $attvId,
                'lan_id' => $value['lan_id'],
                'name' => $name,
            ];
            $attrLanModel->create($insert);
        }
        return true;
    }

    public function getInfoByName($name)
    {
        return make('App\Models\Attrvalue')->getInfoByWhere(['name' => $name]);
    }

    public function getInfo($attvId=null, $lanId=null)
    {
        if (empty($attvId)) {
            return false;
        }
        if (!is_array($attvId)) {
            $attvId = [$attvId];
        }
        $info = make('App\Models\Attrvalue')->whereIn('attv_id', $attvId)->field('attv_id, name')->get();
        if ($lanId > 0 && $lanId != env('DEFAULT_LANGUAGE_ID')) {
            $tempData = make('App\Models\AttrvalueLanguage')->whereIn('attv_id', $attvId)->where('lan_id', $lanId)->field('attv_id, name')->get();
            $tempData = array_column($tempData, 'name', 'attv_id');
            foreach ($info as $key => $value) {
                $info[$key]['name'] = $tempData[$value['attv_id']];
            }
        }
        return $info;
    }
}