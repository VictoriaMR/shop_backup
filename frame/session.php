<?php 

namespace frame;

class Session
{
	public static function set($key, $data = [])
	{
		if (empty($key)) return false;
		$temp = explode('_', $key);
		if (count($temp) > 1) {
			$_SESSION[$temp[0]][str_replace($temp[0].'_', '', $key)] = $data;
		} else {
			$_SESSION[$temp[0]] = $data;
		}
		return true;
	}

	public static function get($name = '') 
	{
		if (empty($name)) return $_SESSION;
		$temp = explode('_', $name);
		$data = $_SESSION[$temp[0]] ?? null;
		if (count($temp) > 1) {
			return $data[str_replace($temp[0].'_', '', $name)] ?? null;
		} else {
			return $data;
		}
	}
}