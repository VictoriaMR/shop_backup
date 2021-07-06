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
		if ($concrete instanceof Closure) {
			return $concrete($this);
		}
		$reflector = new \ReflectionClass($concrete);
		if (!$reflector->isInstantiable()) {
			return $concrete;
		}
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

	private function resolvedNonClass(ReflectionParameter $parameter)
	{
		if($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}
		return false;
	}

	private function resolvedClass(ReflectionParameter $parameter)
	{
		return $this->build($parameter->getType()->getName());
	}
}