<?php
class App 
{
	private static $_instance;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function convertToline($name)
	{
		return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
	}

	public static function convertToUp($str, $ucfirst=false)
	{
		$str = ucwords(str_replace('_', ' ', $str));
		$str = str_replace(' ','',lcfirst($str));
		return $ucfirst ? ucfirst($str) : $str;
	}

	public function run() 
	{
		//初始化方法
		$this->init();
		//注册异常处理
		\frame\Error::instance()->register();
		$this->send();
	}

	public function send()
	{
		//路由解析
		$info = router()->analyze()->getRoute();
		//中间件
		\app\middleware\VerifyToken::instance()->handle($info);
		//静态js,css
		if ($info['class'] == 'Admin') {
			html()->buildJs(['jquery', 'common', 'bootstrap', 'bootstrap-plugin']);
			html()->buildCss(['computer/common', 'computer/bootstrap', 'computer/space', 'icon']);
		} else {
			html()->buildJs(['jquery', 'common']);
			html()->buildCss(['icon', (APP_IS_MOBILE ? 'mobile/common' : 'computer/common')]);
			if (empty(session()->get('site_language_name'))) {
				session()->set('site_language_name', 'en');
			}
		}
		//执行方法
		$class = 'app\\controller\\'.$info['class'].'\\'.$info['path'].'Controller';
		if (is_callable([self::autoload($class), $info['func']])) {
			call_user_func_array([self::autoload($class), $info['func']], []);
		}
		$this->runover();
	}

	public function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
	}

	public function make($abstract)
	{
		return $this->autoload($abstract);
	}

	private function autoload($abstract) 
	{
		$file = strtr($abstract, '\\', DS);
		$tempArr = explode(DS, $abstract);
		$tempAbs = array_pop($tempArr);
		$file = ROOT_PATH.implode(DS, $tempArr).DS.ucfirst($tempAbs).'.php';
		if (is_file($file)) {
			return \frame\Container::instance()->autoload(strtr($abstract, DS, '\\'), $file);
		}
		if (!env('APP_DEBUG')) {
			throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
		} else {
			redirect(url(404));
		}
	}

	private function runover()
	{
		if (env('APP_DEBUG')) {
			\frame\Debug::runlog();
			\frame\Debug::init();
		}
		exit();
	}
}