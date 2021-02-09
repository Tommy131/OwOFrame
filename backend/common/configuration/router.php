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
	* 此配置文件为域名绑定规则的配置文件.
	* This configuration is for bind domain(s) to application.

************************************************************************/

use backend\system\route\RouteRule as RR;

// 绑定域名 test.xxx.com 到 IndexApp;
RR::domain('xxx.com', ['test' => 'index']);

// 添加一个Api处理器;
RR::bindApiProcessor(new class extends backend\system\http\ApiProcessor
{
	public function start(array $params) : string
	{
		return 'ok';
	}

	public static function getName() : string
	{
		return 'test';
	}

	public static function getVersion() : string
	{
		return "v1.0.0";
	}

	public static function mode() : int
	{
		return -1; // For details, please see the comments of the parent class 
	}
});