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
// 基础全局配置 | Base Global Configuration;
$config =
[
	// 开发者模式 | DEBUG_MODE;
	'DEBUG_MODE'       => true,
	// 记录错误日志 | LOG_ERROR;
	'LOG_ERROR'        => false,
	// 默认App名称 | Default application Name;
	'DEFAULT_APP_NAME' => 'index', // Change here to select a default application;
	// 不允许通过HTTP路由访问的App |  Disallow the applications array from the HTTP Router;
	'DENY_APP_LIST'    => [],
	// 默认时区 | Default timezone;
	'TIME_ZONE'        => 'Europe/Berlin'
];
foreach($config as $define => $param) {
	if(!defined($define)) define($define, $param);
}

// 引入自动加载文件 | require autoload file;
$classLoader = require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
$master = new owoframe\MasterManager($classLoader);
$http = $master->getManager('http');
$http->start();
$master->stop();