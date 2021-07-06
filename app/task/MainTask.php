<?php

namespace app\task;

class MainTask
{
	const TIME_OUT = 1000;
	
	public function start()
	{
		if (IS_CLI) {
			return;
		}
		$taskClass = 'app\task\MainTask';
	}
}