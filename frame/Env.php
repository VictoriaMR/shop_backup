<?php
if (is_file(ROOT_PATH . '.env')) {
	$GLOBALS['ENV'] = parse_ini_file(ROOT_PATH . '.env', true);
}
$GLOBALS['ENV']['APP_DOMAIN'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/';