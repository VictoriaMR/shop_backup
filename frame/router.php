<?php
final class Router
{
	public static $_route = []; //路由

	public static function analyze()
	{
		$pathInfo = trim($_SERVER['REQUEST_URI'], DS);
		self::$_route['class'] = ucfirst(APP_TEMPLATE_TYPE);
		if (empty($pathInfo)) {
			self::$_route['path'] = 'Index';
			self::$_route['func'] = 'index';
		} else {
			$pathInfo = parse_url($pathInfo);
			if (empty($pathInfo['path'])) {
				self::$_route['path'] = 'Index';
				self::$_route['func'] = 'index';
			} else {
				$pathInfo['path'] = str_replace('.html', '', $pathInfo['path']);
				if (strpos($pathInfo['path'], '-') === false) {
					$pathInfo = explode(DS, $pathInfo['path']);
			        switch (count($pathInfo)) {
			        	case 0:
			        		self::$_route['path'] = 'Index';
				        	self::$_route['func'] = 'index';
			        		break;
			        	case 1:
			        		self::$_route['path'] = ucfirst(implode(DS, $pathInfo));
				        	self::$_route['func'] = 'index';
			        		break;
			        	default:
			        		$func = array_pop($pathInfo);
			        		self::$_route['path'] = ucfirst(implode(DS, $pathInfo));
			        		self::$_route['func'] = lcfirst($func);
			        		break;
			        }
				} else {
					$pathInfo = explode('-', $pathInfo['path']);
					$temp = array_pop($pathInfo);
					//是页码
					if (strlen($temp) > 1 && strpos($temp, 'p') === 0) {
						$_GET['page'] = (int)substr($temp, 1);
						$temp = array_pop($pathInfo);
					}
					$id = (int) $temp;
					$temp = array_pop($pathInfo);
					switch ($temp) {
						case 'c':
							self::$_route['path'] = 'Category';
							$_GET['cid'] = $id;
							break;
						case 'p':
						case 'k':
							self::$_route['path'] = 'Product';
							$_GET['s'.$temp.'u_id'] = $id;
							break;
						default:
							self::$_route['path'] = 'Index';
							break;
					}
					self::$_route['func'] = 'index';
				}
			}
		}
		array_shift($_GET);
		if (count(self::$_route) != 3) {
			throw new \Exception(' router analyed error', 1);
		}
		return true;
	}

	public static function buildUrl($url = null, $param = null)
	{
		if (is_null($url)) {
			$url = lcfirst(self::$_route['path']) . DS . lcfirst(self::$_route['func']);
		}
		if (!empty($url)) {
			$url .= '.html';
		}
		if (empty($param)) {
			$param = iget();
		}
		if (!empty($param)) {
			$url .= '?' . http_build_query($param);
		}
		return env('APP_DOMAIN').$url;
	}
}