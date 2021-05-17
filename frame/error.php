<?php

namespace frame;

class Error
{
	private static $_error = [];
    
    public static function register()
    {
        if (env('APP_DEBUG')) {
			error_reporting(E_ALL);
		} else {
			error_reporting(0);
		}
		set_error_handler([__CLASS__, 'error_debug']);
        set_exception_handler([__CLASS__, 'exception_debug']);
        register_shutdown_function([__CLASS__, 'shutdown_debug']);
	}

    public static function error_debug($errno, $errStr, $errfile = '', $errline = '')
    {
		self::error_echo($errfile, $errline, $errStr);
    }

    public static function exception_debug($exception)
    {
    	self::error_echo($exception->getFile(), $exception->getLine(), $exception->getMessage());
    }

    public static function shutdown_debug()
	{
		$_error = error_get_last();
		if ($_error) {
			self::error_echo($_error['file'], $_error['line'], $_error['message']);
		}
	}

    public static function error_echo($file, $line, $message)
	{
		if (!isCli()) {
			\frame\Debug::runlog($message);
			if (env('APP_DEBUG')) {
				echo 'File: '.$file.'<br />';
				echo 'Line: '.$line.'<br />';
				echo 'Error Message: '.$message.'<br />';
				echo 'Uri: '.($_SERVER['REQUEST_METHOD'] ?? '').' '.($_SERVER['HTTP_HOST'] ?? '').' '.($_SERVER['REQUEST_URI'] ?? '').'<br />';
				echo 'Index: '.implode('/', \Router::$_route).'<br />';
				echo 'Param: '.json_encode(input()).'<br />';
			} else {
				echo 'Error Message: '.$message.'<br />';
			}
		}
		exit();
	}
}