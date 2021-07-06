<?php 

namespace frame;

class Task
{
	public function start($taskClass='', $lockTimeout=0, $data=[])
	{
		if (empty($taskClass)) {
			$taskClass = 'core\task\MainTask';
		} else {
			$taskClass = $this->getStandClassName($taskClass);
		}
		if($lockTimeout < 1){
			$lockTimeout = config('task')['timeout'];
		}
		if(!isset($data['ip'])){
			$data['ip'] = request()->getIp();
		}
		$lockKey = $this->getKeyByClassName($taskClass);
		$locker = make('frame/Locker');
        if ($locker->lock($lockKey, $lockTimeout)) {
           $cas = $locker->holdLock($lockKey);
           // 启动主进程
           $process = [
               'class' => $taskClass,
               'lock' => [$lockKey, $cas],
               'data' => $data,
           ];
           dd($process);
           $this->run($process);
       }
       dd('123123');
	}

	protected function getStandClassName($classname)
	{
		return trim($classname, ' \t\n\r\0\x0B\\');
	}

	protected function getKeyByClassName($classname)
	{
		return str_replace('\\', '-', $classname);
	}

	protected function run($process)
	{

	}
}