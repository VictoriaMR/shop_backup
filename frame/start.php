<?php
//框架文件加载
require ROOT_PATH.'frame/App.php';
require ROOT_PATH.'frame/Container.php';
//基础方法加载
require ROOT_PATH.'frame/Helper.php';
if (is_file(ROOT_PATH.'frame/Env.php')) {
	require ROOT_PATH.'frame/Env.php';
}
if (is_file(ROOT_PATH.'frame/Config.php')) {
	require ROOT_PATH.'frame/Config.php';
}
if (is_file(ROOT_PATH.'vendor/Autoload.php')) {
	require ROOT_PATH.'vendor/Autoload.php';
}
if (isCli()) {
	App::instance()->init();
} else {
	@session_start();
	define('APP_IS_MOBILE', isMobile());
	App::instance()->run();
}