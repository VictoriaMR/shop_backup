<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\Site;

class SiteService extends BaseService
{
    public function __construct(Site $model)
    {
        $this->baseModel = $model;
    }

	public function getLanguage(array $where)
    {
        return make('App\Models\SiteLanguage')->getListByWhere($where);
    }

    public function setNxLanguage($siteId, $name, $lanId, $value)
    {
        if (empty($siteId) || empty($name) || empty($lanId) || empty($value)) {
            return false;
        }
        $model = make('App\Models\SiteLanguage');
        $where = ['site_id'=>$siteId, 'name'=>$name, 'lan_id'=>$lanId];
        if ($model->getCount($where)) {
            return $model->where($where)->update(['value' => $value]);
        } else {
            $where['value'] = $value;
            return $model->insert($where);
        }
    }
}