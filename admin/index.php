<?php
ini_set('date.timezone', 'Asia/Shanghai');
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(realpath(dirname(__FILE__).'/../').'/', '\\', '/'));
define('APP_TEMPLATE_TYPE', 'admin');
define('APP_STATIC', false);
define('APP_DOMAIN', 'https://lmr.admin.cn/');
require ROOT_PATH.'frame/start.php';