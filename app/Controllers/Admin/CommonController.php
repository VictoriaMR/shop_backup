<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class CommonController extends Controller
{
	public function index()
	{	
		dd('error');
	}

	public function getCrawerData()
	{
		$data = [
			'version' => '1.0.0',
			'category' => make('App\Services\CategoryService')->getListFormat(),
		];
		$this->success($data);
	}
}