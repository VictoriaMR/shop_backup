<?php

namespace app\task;

abstract class TaskDriver
{
	const TASKPREFIX ='frame-task:';
	const TASKUDPSERVERKEY = 'frame-task:udp-block-server';
	protected $startTime;
	protected $isRealObject = true;
	protected $lock ='';
	protected $cas ='';
	protected $ip = '';
	protected $data = '';
	protected $continueRuning = true;
	protected $key = '';
	public $config = [
		'info' => '任务说明',
		'task_url' => '',
		'task_ip' => '',
		'cron' => [
			// 按下面格式配置， 可同时配置多条
			// * * * * * *	分 时 日 月 周 (全为*表示持续运行)
			// 0 3 * * * *”	数字精确配置, 星号为任意.(每天凌晨3点整)
			// 15,30 3 * * *	逗号表示枚举 (每天3点15分和3点30分)
			// 15-30 3 * * *	短线表示范围 (每天的3点15分到30分持续运行)
			// 0-30/10 3 * * *	斜杠表示间隔 (每天3点0分到30分之间, 每10分钟一次)
			// */10 5-8 * * *	斜杠表示间隔 (每天5-8点, 每10分钟一次)
		],
	];
	protected $lockTimeout = 600;
	protected $runCountLimit =-1;
	protected $runTimeLimit = 0;
	protected $sleep = 30;

	public function __construct($process=[])
	{
		if ($process===false) {
			$this->isRealObject = false;
		} else {
			set_time_limit(0);
			$process['lock'] = json_decode(base64_decode($process['lock']), true);
            $process['data'] = json_decode(base64_decode($process['data']), true);
			list($this->key, $this->cas) = $process['lock'];
			if (isset($process['data'])) {
				$this->data = $process['data'];
				if (isset($process['data']['ip'])) {
					$this->ip = $process['data']['ip'];
				}
			}
			$this->startTime = time();
			redis(2)->sAdd(self::TASKPREFIX . 'all', $this->key);
			// 设置任务当次启动时间
			$this->setInfo('start_time', now());
			$this->setInfo('ip', $this->ip);
			$this->setInfo('status', 'runing');
			$this->setInfo('process.pid', getmypid());
			$this->setInfo('process.uid', getmyuid());
			$this->setInfo('process.gid', getmygid());
			$this->setInfo('process.user', get_current_user());
			redis(2)->hIncrBy(self::TASKPREFIX.$this->key, 'count', 1);
			redis(2)->hDel(self::TASKPREFIX.$this->key, 'loopCount');
			$this->startUp();
		}
	}

	public function setInfo($field, $value, $key='')
	{
		$hkey = self::TASKPREFIX.make('frame/Task')->getKeyByClassName($this->key);
		return redis(2)->hSet($hkey, $field, $value);
		return $result;
	}

	public function startUp()
	{
		return $this->setInfo('boot', 'on');
	}
}