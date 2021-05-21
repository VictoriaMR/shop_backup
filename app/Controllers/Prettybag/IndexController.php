<?php

namespace App\Controllers\Prettybag;

use App\Controllers\Controller;
use frame\Html;

class IndexController extends Controller
{
	public function index()
	{	
		Html::addCss();
		Html::addJs();
		Html::addJs('cart');

		//分类列表
		$hotCategory = make('App\Services\CategoryService')->getHotCategory();

		$this->assign('hot_category', $hotCategory);
		return view();
	}
}