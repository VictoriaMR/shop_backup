<?php

namespace frame;

class Redis
{
	private static $_instance;
    private static $_link;
    const DEFAULT_EXT_TIME = 60;
    const DEFAULT_CONNECT_TIME = 5;

	public static function getInstance($db = 0) 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
            try {
                self::$_link = new \Redis();
                self::connect();
            } catch (\Exception $e) {
                self::$_link = null;
            }
        }
        if (!is_null(self::$_link)) {
            self::$_link->select($db);//选择数据库
        }
        return self::$_instance;
    }

    private static function connect() 
    {
        self::$_link->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', '6379'), self::DEFAULT_CONNECT_TIME);
        if (!empty(env('REDIS_PASSWORD'))) {
            self::$_link->auth(env('REDIS_PASSWORD'));
        }
        return true;
    }

    public function get($key)
    {
        if (is_null(self::$_link)) return false;
        $data = self::$_link->get($key);
        $temp = isJson($data);
        if ($temp === false) {
            return $data;
        } else {
            return $temp;
        }
    }

    public function set($key, $value, $ext=null) 
    {
        if (is_null(self::$_link)) return false;
        if (empty($key)) return false;
        $ext = is_null($ext) ? self::DEFAULT_EXT_TIME : $ext;
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($ext > 0) {
            return self::$_link->set($key, $value, $ext);
        } else {
            return self::$_link->set($key, $value);
        }
    }

    public function __call($func, $arg)
    {
        if (is_null(self::$_link)) return false;
        if ($func == 'hmset') {
            foreach ($arg as $key => $value) {
                if (is_array($value)) {
                    $arg[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }   
        }
        $info = self::$_link->$func(...$arg);
        $temp = isJson($info);
        if ($temp === false) {
            return $info;
        } else {
            return $temp;
        }
    }
}