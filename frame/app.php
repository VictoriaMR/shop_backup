<?php
class App 
{
	private static $_instance = null;

    public static function instance() 
    {
    	if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }

	public static function run() 
	{
		//初始化方法
		self::init();
        //注册异常处理
        \frame\Error::register();
		//解析路由
        Router::analyze();
		return self::instance();
	}

    public function send()
    {
        //路由解析
        $info = Router::$_route;
        //中间件
        \App\Middleware\VerifyToken::handle($info);
        //静态js,css
        if ($info['class'] == 'Admin') {
            \frame\Html::buildJs(['jquery', 'common', 'bootstrap', 'bootstrap-plugin']);
            \frame\Html::buildCss(['computer/common', 'computer/bootstrap', 'computer/space', 'icon']);
        } elseif ($info['class'] == 'Home') {
            \frame\Html::buildJs(['jquery', 'common']);
            \frame\Html::buildCss(['icon', (isMobile() ? 'mobile/common' : 'computer/common')]);
        }
        //执行方法
        $class = 'App\\Controllers\\'.$info['class'].'\\'.$info['path'].'Controller';
        if (is_callable([self::autoload($class), $info['func']])) {
            call_user_func_array([self::autoload($class), $info['func']], []);
        }
        $this->runover();
    }

    public function load($template = '')
    {
        return \frame\View::load($template);
    }

	public static function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
	}

	private static function autoload($abstract) 
    {
        $abstract = strtr($abstract, '/', '\\');
        //容器加载
        if (!empty(Container::$_building[$abstract])) {
            return Container::$_building[$abstract];
        }
        $file = strtr($abstract, '\\', DS);
        if (strpos($file, 'App') === 0) {
            $file = lcfirst($file);
        } else if (strpos($file, 'frame') !== false) {
            $file = strtolower($file);
        }
        $file = ROOT_PATH.$file.'.php';
        if (is_file($file)) {
			require_once $file;
        } else {
            if (env('APP_DEBUG')) {
                throw new \Exception($abstract.' was not exist!', 1);
            } else {
                redirect(url());
            }
        }
		return Container::getInstance()->autoload($abstract);
    }

    public static function make($abstract)
    {
    	return self::autoload($abstract);
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