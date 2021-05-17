#!/usr/bin/env php
<?php 
ini_set('date.timezone', 'Asia/Shanghai');
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(dirname(__FILE__), '\\', '/').'/');
require ROOT_PATH.'frame/start.php';
$kernal = make('App/Console/Kernal');
$kernal->run();
exit();