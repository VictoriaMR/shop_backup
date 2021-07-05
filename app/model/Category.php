<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Category extends BaseModel
{
    //表名
    protected $_table = 'category';
    //主键
    protected $_primaryKey = 'cate_id';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}