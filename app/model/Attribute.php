<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Attribute extends BaseModel
{
    //表名
    protected $_table = 'attribute';
    //主键
    protected $_primaryKey = 'attr_id';

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}