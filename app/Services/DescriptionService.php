<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	产品Spu数据类
 */
class DescriptionService extends BaseService
{
    public function setNotExit($name)
    {
    	if (empty($name)) {
    		return 0;
    	}
    	$model = make('App\Models\Description');
    	$info = $model->getInfoByWhere(['name' => $name]);
    	if (empty($info)) {
    		$id = $model->insertGetId(['name' => $name]);
            //翻译
            $lanArr = make('App\Services\LanguageService')->getInfoCache();
            $model = make('App\Models\DescriptionLanguage');
            $translateService = make('App\Services\TranslateService');
            foreach ($lanArr as $key => $value) {
                if ($value['code'] == 'zh') {
                    $tempName = $name;
                } else {
                    $tempName = $translateService->getTranslate($name, $value['tr_code']);
                }
                $insert = [
                    'desc_id' => $id,
                    'lan_id' => $value['lan_id'],
                    'name' => $tempName,
                ];
                $model->insert($insert);
            }
    	}
    	return $info['desc_id'];
    }

    public function addDescRelation(array $insert)
    {
    	if (empty($insert)) {
    		return false;
    	}
    	$model = make('App\Models\ProductDescRelation');
    	if (!empty($insert[0]) && is_array($insert[0])) {
            foreach ($insert as $key => $value) {
                if ($model->getCount($value)) {
                    unset($insert[$key]);
                }
            }
        }
        return $model->insert($insert);
    }
}