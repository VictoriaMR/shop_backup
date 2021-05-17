<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductSpuData extends BaseModel
{
    //表名
    protected $_table = 'product_spu_data';
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

    public function isExist($siteId, $itemId)
    {
        return $this->getCount(['site_id' => (int)$siteId, 'item_id' => (int)$itemId]) > 0;
    }
}