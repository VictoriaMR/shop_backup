<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Attrvalue extends BaseModel
{
    //表名
    protected $_table = 'attrvalue';
    //主键
    protected $_primaryKey = 'attv_id';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}