<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductSpuImage extends BaseModel
{
    //表名
    protected $_table = 'product_spu_image';

    public function create(array $data) 
    {
    	return $this->insert($data);
    }

    public function getInfoBySpuId($spuId)
    {
        $info = $this->where(['spu_id'=>(int)$spuId])->field('attach_id')->get();
        if (empty($info)) {
            return [];
        }
        return array_column($info, 'attach_id');
    }
}