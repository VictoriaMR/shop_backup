<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class ProductController extends Controller
{
	public function __construct()
	{
        $arr = [
            'index' => '产品列表',
            'cateList' => '分类管理',
        ];
		$this->_nav = array_merge(['default' => '产品管理'], $arr);
		$this->_init();
	}

	public function index()
	{	
		dd('error');
	}

	public function create()
	{
		set_time_limit(120);
		$data = ipost();
		if (empty($data['bc_product_category'])) {
			$this->error('产品分类不能为空!');
		}
		$supplierId = $this->getSiteId($data['bc_site_id']);
		if (empty($supplierId)) {
			$this->error('供应商不能为空!');
		}
		if (empty($data['bc_product_site'])) {
			$this->error('站点不能为空!');
		}
		if (empty($data['bc_sku'])) {
			$this->error('产品SKU不能为空!');
		}
		if (empty($data['bc_product_img'])) {
			$this->error('产品图片不能为空!');
		}
		$data['bc_product_url'] = explode('?', $data['bc_product_url'])[0] ?? '';
		//查询产品是否入库
		$spuDataService = make('App\Services\ProductSpuDataService');
		
		$translateService = make('App\Services\TranslateService');
		$fileService = make('App\Services\FileService');

		//Spu图片
		$spuImageArr = [];
		$firstImage = [];
		if (!is_array($data['bc_product_img'])) {
			$data['bc_product_img'] = explode(',', $data['bc_product_img']);
		}
		foreach ($data['bc_product_img'] as $key => $value) {
			$url = $this->filterUrl($value);
			$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'product');
			if ($key == 0) {
				$firstImage = $spuImageArr[$url];
			}
		}
		$spuImageArr = array_filter($spuImageArr);
		if (empty($spuImageArr)) {
			$this->error('产品图片上传失败!');
		}
		//属性组
		$attributeService = make('App\Services\AttributeService');
		$attrvalueService = make('App\Services\AttrvalueService');
		$attrArr = [];
		$attrValueArr = [];
		foreach ($data['bc_sku'] as $key => $value) {
			$attrArr = array_merge($attrArr, array_keys($value['attr']));
			$attrValueArr = array_merge($attrValueArr, array_column($value['attr'], 'text'));
		}
		$attrArr = array_flip(array_unique($attrArr));
		$attrValueArr = array_flip(array_unique($attrValueArr));
		foreach ($attrArr as $key => $value) {
			$attrArr[$key] = $attributeService->addNotExist($key);
		}
		foreach ($attrValueArr as $key => $value) {
			$attrValueArr[$key] = $attrvalueService->addNotExist($key);
		}
		//多语言配置
		$lanArr = make('App\Services\LanguageService')->getInfoCache();

		$productLanguageService = make('App\Services\ProductLanguageService');

		$where = [
			'site_id' => $data['bc_product_site'],
			'supplier_id' => $supplierId,
			'item_id' => $data['bc_product_id'],
		];
		$info = $spuDataService->getInfoByWhere($where);
		if (empty($info)) {
			//价格合集
			foreach ($data['bc_sku'] as $key => $value) {
				$data['bc_sku'][$key]['p_price'] = $value['price'] + rand(250, 350);
			}
			$priceArr = array_column($data['bc_sku'], 'p_price');
			$insert = [
				'status' => 1,
				'site_id' => $data['bc_product_site'],
				'avatar' => $firstImage['cate'].'/'.$firstImage['name'].'.'.$firstImage['type'],
				'min_price' => min($priceArr),
				'max_price' => max($priceArr),
				'origin_price' => round(max($priceArr) * ((10 - rand(5, 9)) / 10 + 1), 2),
				'name' => $data['bc_product_name'],
				'add_time' => now(),
			];
			$spuService = make('App\Services\ProductSpuService');
			//事务开启
			$spuDataService->start();
			$spuId = $spuService->create($insert);
			//spu扩展数据
			$insert = [
				'spu_id' => $spuId,
				'site_id' => $data['bc_product_site'],
				'supplier_id' => $supplierId,
				'item_id' => $data['bc_product_id'],
				'item_url' => $data['bc_product_url'],
				'shop_name' => $data['bc_shop_name'],
				'shop_url' => $data['bc_shop_url'],
			];
			$spuDataService->create($insert);
			//sku
			$skuService = make('App\Services\ProductSkuService');
			foreach ($data['bc_sku'] as $key => $value) {
				$nameZhStr = '';
				//sku 属性
				foreach ($value['attr'] as $k => $v) {
					$nameZhStr .= ' '.$v['text'];
				}
				$nameZhStr = trim($nameZhStr);
				$name = trim($data['bc_product_name'].(empty($nameEnStr) ? '' : ' - '.$nameEnStr));

				$avatar = '';
				if (!empty($value['img'])) {
					$value['img'] = $this->filterUrl($value['img']);
					if (empty($spuImageArr[$value['img']])) {
						$spuImageArr[$value['img']] = $fileService->uploadUrlImage($value['img'], 'product');
					}
					$avatar = $spuImageArr[$value['img']]['cate'].'/'.$spuImageArr[$value['img']]['name'].'.'.$spuImageArr[$value['img']]['type'];
				}
				$insert = [
					'spu_id' => $spuId,
					'status' => $value['stock'] > 0 ? 1 : 0,
					'avatar' => $avatar,
					'stock' => $value['stock'],
					'price' => $value['p_price'],
					'cost_price' => $value['price'],
					'name' => $name,
					'add_time' => now(),
				];
				$skuId = $skuService->create($insert);
				//多语言
				foreach ($lanArr as $k => $v) {
					if ($v['code'] != 'zh') {
						$tempName = $translateService->getTranslate($name, $v['tr_code']);
					} else {
						$tempName = $name;
					}
					$insert = [
						'spu_id' => $spuId,
						'sku_id' => $skuId,
						'lan_id' => $v['lan_id'],
						'name' => $tempName,
					];
					$productLanguageService->create($insert);
				}
				//属性关联
				$insert = [];
				$count = 1;
				foreach ($value['attr'] as $k => $v) {
					if (empty($v['img'])) {
						$attachId = 0;
					} else {
						$v['img'] = $this->filterUrl($v['img']);
						if (empty($spuImageArr[$v['img']])) {
							$spuImageArr[$v['img']] = $fileService->uploadUrlImage($v['img'], 'product');
						}
						$attachId = $spuImageArr[$v['img']]['attach_id'];
					}
					$insert[] = [
						'sku_id' => $skuId,
						'attr_id' => $attrArr[$k],
						'attv_id' => $attrValueArr[$v['text']],
						'attach_id' => $attachId,
						'sort' => $count++,
					];
				}
				if (!empty($insert)) {
					$skuService->addAttributeRelation($insert);
				}
			}
			$spuDataService->commit();
		} else {
			$spuId = $info['spu_id'];
		}
		if (empty($spuId)) {
			$this->error('产品SPU入库失败!');
		}
		//产品分类关联
		make('App\Services\CategoryService')->addCateProRelation($spuId, $data['bc_product_category']);

		foreach ($lanArr as $key => $value) {
			if ($value['code'] != 'zh') {
				$name = $translateService->getTranslate($data['bc_product_name'], $value['tr_code']);
			} else {
				$name = $data['bc_product_name'];
			}
			$insert = [
				'spu_id' => $spuId,
				'sku_id' => 0,
				'lan_id' => $value['lan_id'],
				'name' => $name,
			];
			$productLanguageService->create($insert);
		}
		//spu图片组
		$insert = [];
		$count = 1;
		foreach ($spuImageArr as $value) {
			$insert[] = [
				'spu_id' => $spuId,
				'attach_id' => $value['attach_id'],
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			$spuService->addSpuImage($insert);
		}
		//spu 介绍图片
		$insert = [];
		$count = 1;
		$data['bc_product_des_picture'] = explode(',', $data['bc_product_des_picture']);
		foreach ($data['bc_product_des_picture'] as $value) {
			$url = $this->filterUrl($value);
			if (empty($spuImageArr[$url])) {
				$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'introduce', false);
			}
			if (empty($spuImageArr[$url]['attach_id'])) continue;
			$insert[] = [
				'spu_id' => $spuId,
				'attach_id' => $spuImageArr[$url]['attach_id'],
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			$spuService->addIntroduceImage($insert);
		}

		//介绍文本
		$descService = make('App\Services\DescriptionService');
		$descArr = [];
		$insert = [];
		foreach ($data['bc_des_text'] as $key => $value) {
			$value['key'] = trim($value['key']);
			$value['value'] = trim($value['value']);
			$descArr[$value['key']] = $descService->setNotExit($value['key']);
			$descArr[$value['value']] = $descService->setNotExit($value['value']);
			$insert[] = [
				'spu_id' => $spuId,
				'name_id' => $descArr[$value['key']],
				'value_id' => $descArr[$value['value']],
			];
		}
		$descService->addDescRelation($insert);
		$this->success();
	}

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800'], '', $url);
	}

	protected function getSiteId($name)
	{
		$siteIdArr = [
			'1688' => 1,
			'taobao' => 2,
			'tmall' => 3
		];
		return $siteIdArr[$name] ?? 0;
	}
}