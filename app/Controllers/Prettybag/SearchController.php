<?php

namespace App\Controllers\Prettybag;

use App\Controllers\Controller;
use frame\Html;

class SearchController extends Controller
{
	public function index()
	{	
		appT('搜索宝贝');
		$this->assign('_title', appT('搜索宝贝'));
		return view();
	}
}