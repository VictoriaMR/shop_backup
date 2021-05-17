<?php

namespace frame;

class Connection
{
	private $conn;
	private static $_instance = [];
	private function __construct() {}
	private function __clone() {}

	private static function connect($host, $username, $password, $port = '3306', $database = '', $charset='utf8')
	{
		$conn =  new \mysqli($host, $username, $password, $database, $port);
		if($conn->connect_error){
			throw new \Exception('Connect Error ('.$conn->connect_errno.') '.$conn->connect_error, 1);
		}
		$conn->set_charset($charset);
		return $conn;
	}

	/**
	 * @method 数据库链接单例方法
	 * @date   2020-05-25
	 */
	public static function getInstance($db = null, $database = null) 
	{
		if (is_null($db)) $db = 'default';
		if (is_null($database)) $database = env('DB_DATABASE');

		if (empty(self::$_instance[$db][$database])) {
			$config = dbconfig($db);
			if (empty($config)) {
				throw new \Exception('Connect Error： Cannot found '.$db.' in config/database', 1);
			}
			self::$_instance[$db][$database] = self::connect(
				$config['db_host'] ?? '', 
				$config['db_username'] ?? '', 
				$config['db_password'] ?? '', 
				$config['db_port'] ?? '', 
				$config['db_database'] ?? '', 
				$config['db_charset'] ?? ''
			);
		}
		return self::$_instance[$db][$database];
	}
}