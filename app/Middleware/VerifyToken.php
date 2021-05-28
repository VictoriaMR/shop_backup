<?php

namespace App\Middleware;

use frame\Session;

class VerifyToken
{
    protected static $except = [
        'admin' => [
            'login' => true,
        ],
        'prettybag' => [
            'index' => true,
            'login' => true,
        ],
    ];

    public static function handle($request)
    {
        if (self::inExceptArray($request)) {
            return true;
        }
        if ($request['class'] == 'Admin') {
            $loginKey = 'admin_mem_id';
        } else {
            $loginKey = 'home_mem_id';
        }
        //检查登录状态
        if (empty(Session::get($loginKey))) {
            Session::set('callback_url', rtrim($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']), '?');
            if (isAjax()) {
                header('Content-Type:application/json; charset=utf-8');
                echo json_encode(['code'=>'10001', 'data'=>'', 'message' => 'need login'], JSON_UNESCAPED_UNICODE);
                exit();
            } else {
                redirect(url('login'));
            }
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
