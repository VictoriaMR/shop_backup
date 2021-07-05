<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Language extends BaseModel
{
    //表名
    protected $_table = 'language';
    //主键
    protected $_primaryKey = 'lan_id';

    public function getInfo($fields = '')
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}