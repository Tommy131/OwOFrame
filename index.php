<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	*
	* 入口 & 配置文件

************************************************************************/

// 基础全局配置 | Base Global Configuration;
$config =
[
	/* Normal Settings */
	// 开发者模式 | DEBUG_MODE;
	"DEBUG_MODE"               => true,
	// 记录错误日志 | LOG_ERROR;
	"LOG_ERROR"                => false,
	// 默认App名称 | Default application Name;
	"DEFAULT_APP_NAME"         => "index", // Change here to select a default application;
	// 不允许通过HTTP路由访问的App |  Disallow the applications array from the HTTP Router;
	"DENY_APP_LIST"            => [],
	// 最大可读取配置文件行数 | Maximal allowable lines for configuration;
	"CFG_MAX_LIMIT_LINES"      => 1000,
	// 默认时区 | Default timezone;
	"TIME_ZONE"                => "Europe/Berlin",
	// 全局使用JSON格式输出错误 | Use Json format output the error message;
	"GLOBAL_USE_JSON_FORMAT"   => true,
	// 默认情况下连接数据库 | Connect to database automaticly;
	"DEFAULT_DATABASE_CONNECT" => true,

	/* Redis Settings */
	// 使用Redis作为session缓存器 | Change Session storage to Redis Server;
	"USE_REDIS_SESSION"        => true,
	// Redis服务器地址 | Redis Server Address;
	"REDIS_SERVER"             => '127.0.0.1:5300',
	// Redis服务器访问密码 | Redis Server Password (If you haven't set the access password just take with '');
	"REDIS_SERVER_PASSWD"      => 'i7D4Hm4A9lk13To9jU72hG79fC87j7A5'
];
foreach($config as $define => $param) {
	if(!defined($define)) define($define, $param);
}

// 引入引导文件 | require bootstrap file;
require_once(dirname(__FILE__, 1) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'bootstrap.php');
\OwOBootstrap\start();