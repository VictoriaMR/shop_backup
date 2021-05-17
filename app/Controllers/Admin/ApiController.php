<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class ApiController extends Controller
{
	protected $_cateArr = ['category'];

	public function upload()
	{	
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error('上传数据为空');
		}
		$cate = $_POST['cate'] ?? '';
		if (!in_array($cate, $this->_cateArr)) {
			$this->error('没有权限操作'.$cate.'文件夹');
		}
		$fileService = make('App\Services\FileService');
		$result = $fileService->upload($file, $cate);
		if (empty($result)) {
			$this->error('上传失败');
		}
		$this->success($result);
	}

	public function stat()
	{
		
		make('App\Services\LoggerService')->addLog();
	}
}