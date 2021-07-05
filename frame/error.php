<?php

namespace frame;

class Error
{
	private static $_instance = null;

	public static function instance() 
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function register()
	{
		if (env('APP_DEBUG')) {
			error_reporting(E_ALL);
		} else {
			error_reporting(0);
		}
		set_error_handler([self::instance(), 'errorDebug'], E_ALL);
		set_exception_handler([self::instance(), 'exceptionDebug']);
		register_shutdown_function([self::instance(), 'shutdownDebug']);
	}

	public function errorDebug($errno, $errStr, $errfile = '', $errline = '')
	{
		throw new \ErrorException($errStr, 0, $errno, $errfile, $errline);
	}

	public function exceptionDebug($exception)
	{
		$this->errorEcho($exception->getFile(), $exception->getLine(), $exception->getMessage());
	}

	public function shutdownDebug()
	{
		$_error = error_get_last();
		if ($_error) {
			$this->errorEcho($_error['file'], $_error['line'], $_error['message']);
		}
	}

	protected function echoParmas()
	{

	}

	protected function errorEcho($file, $line, $message)
	{
		if (!isCli()) {
			\frame\Debug::runlog($message);
			if (env('APP_DEBUG')) {
				echo 'File: '.$file.'<br />';
				echo 'Line: '.$line.'<br />';
				echo 'Error Message: '.$message.'<br />';
				echo 'Uri: '.($_SERVER['REQUEST_METHOD'] ?? '').' '.($_SERVER['HTTP_HOST'] ?? '').' '.($_SERVER['REQUEST_URI'] ?? '').'<br />';
				$this->echoParmas();
			} else {
				echo 'Error Message: '.$message.'<br />';
			}
		}
		exit();
	}
}