<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductSpu extends BaseModel
{
    //表名
    protected $_table = 'product_spu';
    //主键
    protected $_primaryKey = 'spu_id';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}