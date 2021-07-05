<?php

namespace frame;

final class Container
{
	static private $_instance;
	private $_building = [];
    private function __construct() {}
    private function __clone() {}

	public static function instance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function autoload($concrete, $file) 
    {
        if (isset($this->_building[$concrete])) {
            return $this->_building[$concrete];
        }
        require $file;
        return $this->build($concrete);
    }

    private function build($concrete)
    {
    	//判断是不是闭包函数
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        //创建反射对象
        $reflector = new \ReflectionClass($concrete);
        //函数是否可以实例化
        if (!$reflector->isInstantiable()) {
            return $concrete;
        }
        //获取构造器
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            $object = new $concrete;
        } else {
            $object = $reflector->newInstanceArgs($this->getDependencies($constructor->getParameters()));
        }
        $this->_building[$concrete] = $object;
        return $object;
    }

    private function getDependencies(array $dependencies)
    {
        $results = [];
        foreach ($dependencies as $dependency) {
            if (is_null($dependency->getType())) {
                $results[] = $this->resolvedNonClass($dependency);
            } else {
                $results[] = $this->resolvedClass($dependency);
            }
        }
        return $results;
    }

    //解决一个没有类型提示依赖
    private function resolvedNonClass(ReflectionParameter $parameter)
    {
        if($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        return false;
    }

    //通过容器解决依赖
    private function resolvedClass(ReflectionParameter $parameter)
    {
        return $this->build($parameter->getType()->getName());
    }
}