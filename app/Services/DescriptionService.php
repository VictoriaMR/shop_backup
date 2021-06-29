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
            $model = make('App\Models\DescriptionLanguage');
            $insert = [
                'desc_id' => $id,
                'lan_id' => 1,
                'name' => $name,
            ];
            make('App\Models\DescriptionLanguage')->insert($insert);
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