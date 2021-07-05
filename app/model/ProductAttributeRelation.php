<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductAttributeRelation extends BaseModel
{
    //表名
    protected $_table = 'product_attribute_relation';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insert($data);
    }
}