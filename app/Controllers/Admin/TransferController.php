<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class TransferController  extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '文本翻译',
        ];
        $this->_default = '站点文本';
		$this->_init();
	}

	public function index()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo'])) {
				$this->$opn();
			}
		}

		Html::addJs();
		$keyword = trim(iget('keyword'));
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 20);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		$service = make('App/Services/TranslateService');
		$total = $service->getCount($where);
		if ($total > 0) {
			$list = $service->getListByWhere($where, '*', $page, $size);
			if (!empty($list)) {
				$languageList = make('App/Services/LanguageService')->getInfo();
				$languageList = array_column($languageList, 'name', 'code');
				foreach ($list as $key => $value) {
					$value['type_name'] = $languageList[$value['type']] ?? '';
					$list[$key] = $value;
				}
			}
		}

		$this->assign('keyword', $keyword);
		$this->assign('size', $size);
		$this->assign('total', $total);
		$this->assign('list', $list ?? '');
		return view();
	}
}