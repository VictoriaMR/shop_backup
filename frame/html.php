<?php 

namespace frame;

class Html
{
    public static $_CSS = [];
    public static $_JS = [];

    public static function addCss($name = '')
    {
        if (empty($name)) {
            $matchPath = '';
            if (env('APP_VIEW_MATCH')) {
                $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer') . DS;
            }
            $_route = \Router::$_route;
            $name = $matchPath . lcfirst($_route['path']) . DS . $_route['func'];
        }
        self::$_CSS[] = 'css' . DS . $name . '.css';
        return true;
    }

    public static function addJs($name = '', $public = false)
    {
        if (empty($name)) {
            $matchPath = '';
            if (env('APP_VIEW_MATCH')) {
                $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer') . DS;
            }
            $_route = \Router::$_route;
            $name = $matchPath . lcfirst($_route['path']) . DS . $_route['func'];
        }
        self::$_JS[] = 'js' . DS . $name . '.js';
        return true;
    }

    public static function getCss()
    {
        if (empty(self::$_CSS)) {
            return [];
        }
        if (count(self::$_CSS) == 1) {
            return self::$_CSS;
        }
        $_route = \Router::$_route;
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.strtolower($_route['path'].'_'.$_route['func']).'.css';
        if (APP_STATIC && is_file($dir.$file)) {
            return ['static'.$file];
        }
        $data = '';
        foreach (self::$_CSS as $key => $value) {
            $source = $path.$value;
            $data .= trim(file_get_contents($source));
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        $data = str_replace(PHP_EOL, '', $data);
        file_put_contents($dir.$file, $data);
        return ['static'.$file];
    }

    public static function getJs()
    {
        if (empty(self::$_JS)) {
            return [];
        }
        if (count(self::$_JS) == 1) {
            return self::$_JS;
        }
        $_route = \Router::$_route;
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.strtolower($_route['path'].'_'.$_route['func']).'.js';
        if (APP_STATIC && is_file($dir.$file)) {
            return ['static'.$file];
        }
        $data = '';
        foreach (self::$_JS as $key => $value) {
            $source = $path.$value;
            $data .= trim(file_get_contents($source));
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        file_put_contents($dir.$file, $data);
        return ['static'.$file];
    }

    public static function buildJs($data)
    {
        if (empty($data)) {
            return false;
        }
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.(isMobile() ? 'm_' : 'c_').'common.js';
        if (APP_STATIC && is_file($dir.$file)) {
            return true;
        }
        if (!is_array($data)) {
            $data = [$data];
        }
        $jsStr = '';
        foreach ($data as $key => $value) {
            $source = $path.'js'.DS.$value.'.js';
            if (is_file($source)) {
                $jsStr .= trim(file_get_contents($source));
            }
        }
        if (empty($jsStr)) {
            return false;
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        return file_put_contents($dir.$file, $jsStr);
    }

    public static function buildCss($data)
    {
        if (empty($data)) {
            return false;
        }
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.(isMobile() ? 'm_' : 'c_').'common.css';
        if (APP_STATIC && is_file($dir.$file)) {
            return true;
        }
        if (!is_array($data)) {
            $data = [$data];
        }
        $cssStr = '';
        foreach ($data as $key => $value) {
            $source = $path.'css'.DS.$value.'.css';
            if (is_file($source)) {
                $cssStr .= trim(file_get_contents($source));
            }
        }
        if (empty($cssStr)) {
            return false;
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        return file_put_contents($dir.$file, $cssStr);
    }
}