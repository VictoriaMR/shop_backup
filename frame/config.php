<?php
if (is_dir(ROOT_PATH . 'config/')) {
	foreach (scandir(ROOT_PATH . 'config/') as $value) {
		if ($value == '.' || $value == '..') continue;
		$GLOBALS[str_replace('.php', '', $value)] = require_once ROOT_PATH . 'config' . DS . $value;
	}
}