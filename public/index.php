<?php

/*********************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2021 OwOBlog-DGMT.
	* Developer: HanskiJay(Tommy131)
	* Telegram:  https://t.me/HanskiJay
	* E-Mail:    support@owoblog.com
	* GitHub:    https://github.com/Tommy131

**********************************************************************/

// Define OwOFrame start time;
if(!defined('START_MICROTIME')) define('START_MICROTIME',  microtime(true));

// 引入自动加载文件 | require autoload file;
$vendor_file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if(!file_exists($vendor_file)) {
	exit('Please execute command \'composer install\' at root path at first!');
}
$classLoader = require_once($vendor_file);
$master = new owoframe\MasterManager($classLoader);
$http = $master->getManager('http');
$http->start();
$master->stop();