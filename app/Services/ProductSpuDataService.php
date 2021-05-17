<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\ProductSpuData;

/**
 * 	产品Spu数据类
 */
class ProductSpuDataService extends BaseService
{
	protected static $constantMap = [
        'base' => ProductSpuData::class,
    ];

	public function __construct(ProductSpuData $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		return $this->baseModel->insert($data);
	}

	public function isExist($siteId, $itemId)
	{
		return $this->baseModel->isExist($siteId, $itemId);
	}
}