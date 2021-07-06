<?php
class App 
{
	public static function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function run() 
	{
		self::init();
		self::send();
	}

	public static function make($abstract)
	{
		return self::autoload($abstract);
	}

	private static function send()
	{
		//路由解析
		$info = router()->analyze()->getRoute();
		//中间件
		self::make('app/middleware/VerifyToken')->handle($info);
		//公共静态js,css
		if (!IS_CLI && !IS_AJAX) {
			if ($info['class'] == 'admin') {
				html()->addJs(['jquery', 'common', 'bootstrap', 'bootstrap-plugin'], false);
				html()->addCss(['computer/common', 'computer/bootstrap', 'computer/space', 'icon'], false);
			} else {
				html()->addJs(['jquery', 'common']);
				html()->addCss(['icon', (IS_MOBILE ? 'mobile/common' : 'computer/common')], false);
				if (empty(session()->get('site_language_name'))) {
					session()->set('site_language_name', 'en');
				}
			}
		}
		//执行方法
		$class = 'app\\controller\\'.$info['class'].'\\'.$info['path'].'Controller';
		call_user_func_array([self::autoload($class), $info['func']], []);
		self::runOver();
	}

	private static function autoload($abstract) 
	{
		$file = ROOT_PATH.str_replace('\\', DS, $abstract).'.php';
		if (is_file($file)) {
			return \frame\Container::instance()->autoload(str_replace(DS, '\\', $abstract), $file);
		}
		if (env('APP_DEBUG')) {
			dd($file, $abstract);
			throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
		} else {
			redirect(url(404));
		}
	}

	private static function runOver()
	{
		if (env('APP_DEBUG')) {
			if (IS_AJAX) {
				self::make('frame/Debug')->runlog();
			} else {
				self::make('frame/Debug')->runlog()->init();
			}
		}
		exit();
	}
}