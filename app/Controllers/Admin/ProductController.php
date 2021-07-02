<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class ProductController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => 'SPU列表',
        ];
		$this->_default = '分类管理';
		$this->_init();
	}

	public function index()
	{	
		$status = (int)iget('status', -1);
		$site = (int)iget('site', -1);
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));
		$spuId = (int)iget('spu_id');

		$spuService = make('App/Services/ProductSpuService');
		$statusList = $spuService->getStatusList();

		$siteList = make('App/Services/SiteService')->getList();
		$siteList = array_column($siteList, 'name', 'site_id');

		$this->assign('spuId', $spuId);
		$this->assign('status', $status);
		$this->assign('statusList', $statusList);
		$this->assign('site', $site);
		$this->assign('siteList', $siteList);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		return view();
	}
}