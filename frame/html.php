<?php 

namespace frame;

class Html
{
    protected $_CSS = [];
    protected $_JS = [];

    private static $_instance;

    public static function instance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function addCss($name = '')
    {
        $matchPath = '';
        if (env('APP_VIEW_MATCH')) {
            $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = router()->getRoute();
            $name = lcfirst($_route['path']) . DS . $_route['func'];
        }
        $this->_CSS[] = 'css' . DS . $matchPath . $name . '.css';
        return true;
    }

    public function addJs($name = '', $public = false)
    {
        $matchPath = '';
        if (env('APP_VIEW_MATCH')) {
            $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = router()->getRoute();
            $name = lcfirst($_route['path']) . DS . $_route['func'];
        }
        $this->_JS[] = 'js' . DS . $matchPath . $name . '.js';
        return true;
    }

    public function getCss()
    {
        if (empty($this->_CSS)) {
            return [];
        }
        if (count($this->_CSS) == 1) {
            return $this->_CSS;
        }
        $_route = router()->getRoute();
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.strtolower($_route['path'].'_'.$_route['func']).'.css';
        if (APP_STATIC && is_file($dir.$file)) {
            return ['static'.$file];
        }
        $data = '';
        foreach ($this->_CSS as $key => $value) {
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

    public function getJs()
    {
        if (empty($this->_JS)) {
            return [];
        }
        if (count($this->_JS) == 1) {
            return $this->_JS;
        }
        $_route = router()->getRoute();
        $path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
        $dir = $path.'static';
        $file = DS.strtolower($_route['path'].'_'.$_route['func']).'.js';
        if (APP_STATIC && is_file($dir.$file)) {
            return ['static'.$file];
        }
        $data = '';
        foreach ($this->_JS as $key => $value) {
            $source = $path.$value;
            $data .= trim(file_get_contents($source));
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        file_put_contents($dir.$file, $data);
        return ['static'.$file];
    }

    public function buildJs($data)
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

    public function buildCss($data)
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