<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductLanguage extends BaseModel
{
    //表名
    protected $_table = 'product_language';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insert($data);
    }

    public function isExist($spuId, $skuId, $lanId)
    {
        return $this->getCount(['spu_id' => (int)$spuId, 'sku_id' => (int)$skuId, 'lan_id' => (int)$lanId]) > 0;
    }
}