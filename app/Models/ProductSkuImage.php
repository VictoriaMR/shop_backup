<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductSkuImage extends BaseModel
{
    //表名
    protected $_table = 'product_sku_image';

    public function create(array $data) 
    {
    	return $this->insert($data);
    }

    public function getInfoBySkuId($skuId)
    {
        $info = $this->where(['sku_id'=>(int)$skuId])->field('attach_id')->get();
        if (empty($info)) {
            return [];
        }
        return array_column($info, 'attach_id');
    }
}