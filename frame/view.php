<?php

namespace frame;

class View 
{
    private static $_instance = null;

    protected static $data = [];

    public static function instance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function display($template='', $match=true)
    {
        return $this->fetch(ROOT_PATH.APP_TEMPLATE_TYPE.DS.'view'.DS.'layout.php', ['layout_include_path' => $this->getTemplate($template, $match)]);
    }

    protected function fetch($template, array $data=[])
    {
        if (is_file($template)) {
            extract(array_merge(self::$data, $data), EXTR_OVERWRITE);
            include $template;
        } else {
            throw new \Exception($template.' was not exist!', 1);
        }
    }

    private function getTemplate($template, $match = true) 
    {
        if ($match) {
            $matchPath = '';
            if (env('APP_VIEW_MATCH')) {
                $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer').DS;
            }
            if (empty($template)) {
                $_route = router()->getRoute();
                $template = $_route['path'].DS.$_route['func'];
            }
            $template = \App::convertToline(APP_TEMPLATE_TYPE.DS.'view'.DS.$matchPath.$template);
        }
        return ROOT_PATH.$template.'.php';
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            self::$data = array_merge(self::$data, $name);
        } else {
            self::$data[$name] = $value;
        }
        return $this;
    }

    public function load($template = '', $match = true)
    {
        $template = self::instance()->getTemplate($template, $match);
        return self::instance()->fetch($template);
    }
}