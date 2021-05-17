<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\ProductLanguage;

/**
 * 	产品语言类
 */
class ProductLanguageService extends BaseService
{
	protected static $constantMap = [
        'base' => ProductLanguage::class,
    ];

	public function __construct(ProductLanguage $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		if ($this->baseModel->isExist($data['spu_id'], $data['sku_id'], $data['lan_id'])) {
			return false;
		} else {
			return $this->baseModel->create($data);
		}
	}

	public function getText($spuId, $skuId, $lanId)
	{
		return $this->baseModel->getInfoByWhere(['spu_id'=>(int)$spuId, 'sku_id'=>(int)$skuId, 'lan_id'=>(int)$lanId], 'name')['name'] ?? '';
	}

	public function getTextArr($spuId, $skuId, $lanId)
	{
		if (!is_array($skuId)) {
			$skuId = [(int) $skuId];
		}
		return $this->baseModel->where(['spu_id'=>(int)$spuId, 'sku_id'=>['in', $skuId], 'lan_id'=>(int)$lanId], 'name')->get();
	}
}