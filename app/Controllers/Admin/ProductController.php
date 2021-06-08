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
}