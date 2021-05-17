<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class CategoryLanguage extends BaseModel
{
    //表名
    protected $_table = 'category_language';

    public function getInfo($cateId, $lanId)
    {
        return $this->getInfoByWhere(['cate_id' => $cateId, 'lan_id' => $lanId]);
    }

    public function existData($cateId, $lanId) 
    {
        return $this->getCount(['cate_id' => $cateId, 'lan_id' => $lanId]) > 0;
    }

    public function create(array $data) 
    {
        if (empty($data['cate_id']) || empty($data['lan_id']) || empty($data['name'])) {
            return false;
        }
        if ($this->existData($data['cate_id'], $data['lan_id'])) {
            return true;
        }
    	return $this->insertGetId($data);
    }
}