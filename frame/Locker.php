<?php

namespace frame;

class Locker
{
	const LOCKERPREFIX = 'frame-lock:';
	protected $lock = [];

	public function lock($name, $timeout=100, $isShareLock=false)
	{
		if ($timeout < 1) {
			$timeout = 100;
		}
		if ($isShareLock) {
			$cas = 'frame-sharelock-'.$timeout;
		} else {
			$cas = make('frame/Str')->random(32);
		}

		$lock = redis(2)->set(self::LOCKERPREFIX.$name, $cas, ['nx', 'ex' => $timeout]);
		if($lock) {
			$this->lock[$name] = $cas;
		}
		return $lock;
	}

	public function holdLock($name)
	{
		$cas = $this->lock[$name] ?? false;
		if ($cas) {
			unset($this->lock[$name]);
		}
		return $cas;
	}

	public function getLock($name, $cas)
	{
		$lock = redis(2)->get(self::LOCKERPREFIX.$name);
		if ($lock == $cas) {
			$this->lock[$name] = $cas;
			return true;
		}
		return false;
	}
}