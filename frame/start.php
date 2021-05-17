<?php
//框架文件加载
require ROOT_PATH.'frame/app.php';
require ROOT_PATH.'frame/router.php';
require ROOT_PATH.'frame/container.php';
require ROOT_PATH.'frame/helper.php';
if (is_file(ROOT_PATH . 'frame/env.php')) {
	require ROOT_PATH . 'frame/env.php';
}
if (is_file(ROOT_PATH . 'frame/config.php')) {
	require ROOT_PATH . 'frame/config.php';
}
if (is_file(ROOT_PATH . 'vendor/autoload.php')) {
	require ROOT_PATH . 'vendor/autoload.php';
}
if (isCli()) {
	App::init();
} else {
	@session_start();
	define('APP_IS_MOBILE', isMobile());
	App::run()->send();
}