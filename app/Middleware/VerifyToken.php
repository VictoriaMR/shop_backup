<?php

namespace App\Middleware;

use frame\Session;

class VerifyToken
{
    protected static $except = [
        'admin' => [
            'login' => true,
            'common' => true,
            'product' => true,
            'api' => true,
        ],
        'home' => [
            'index' => true,
            'api' => true,
            'login' => true,
        ],
    ];

    public static function handle($request)
    {
        if (self::inExceptArray($request)) {
            return true;
        }
        switch ($request['class']) {
            case 'Admin':
                $loginKey = 'admin_mem_id';
                break;
            case 'Home':
                $loginKey = 'home_mem_id';
                break;
        }
        //检查登录状态
        if (!empty($loginKey) && empty(Session::get($loginKey))) {
            Session::set('callback_url', rtrim($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']), '?');
            redirect(url('login'));
        }
        return true;
    }

    private static function inExceptArray($route)
    {
        //没有在排除的都要求登录
        $class = strtolower($route['class']);
        if (empty(self::$except[$class])) {
            return false;
        }
        $path = strtolower($route['path']);
        if (!empty(self::$except[$class][$path])) {
            return true;
        }
        if (!empty(self::$except[$class][$path.'/'.$route['func']])) {
            return true;
        }
        return false;
    }
}
