<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class CommonController extends Controller
{
	public function index()
	{	
		dd('error');
	}

	public function getCrawlerData()
	{
		$data = [
			'version' => '1.0.0',
			'category' => make('App\Services\CategoryService')->getListFormat(),
			'site' => make('App\Services\SiteService')->getList(),
		];
		$this->success($data);
	}
}