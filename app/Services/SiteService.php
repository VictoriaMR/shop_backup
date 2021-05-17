<?php 

namespace App\Services;

use App\Services\Base as BaseService;

class SiteService extends BaseService
{
	public function getInfo($siteId=1)
	{
		return make('App\Models\Site')->loadData($siteId);
	}

	public function updateInfo($siteId=1, array $data)
	{
		return make('App\Models\Site')->updateDataById($siteId, $data);
	}

	public function getLanguage($name)
    {
        if (empty($name)) {
            return [];
        }
        return make('App\Models\SiteLanguage')->getListByWhere(['name' => $name]);
    }

    public function setNxLanguage($name, $lanId, $value)
    {
        if (empty($name) || empty($lanId) || empty($value)) {
            return false;
        }
        $model = make('App\Models\SiteLanguage');
        $where = ['site_id'=>1, 'name'=>$name, 'lan_id'=>$lanId];
        if ($model->getCount($where)) {
            return $model->where($where)->update(['value' => $value]);
        } else {
            $where['value'] = $value;
            return $model->insert($where);
        }
    }
}