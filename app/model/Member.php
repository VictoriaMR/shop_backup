<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Member extends BaseModel
{
    const INFO_CACHE_TIMEOUT = 3600 *24;
    
    //表名
    protected $_table = 'member';
    //主键
    protected $_primaryKey = 'mem_id';

    public function isExistUserByMobile($mobile)
    {
        if (empty($mobile)) return false;
        return $this->getCount(['mobile' => $mobile])['count'] ?? 0;
    }

    public function getInfoByMobile($mobile)
    {
    	return $this->getInfoByWhere(['mobile' => $mobile]);
    }

    public function getInfo($userId)
    {
        return $this->loadData($userId);
    }
}