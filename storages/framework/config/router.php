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

use owoframe\http\route\RouteRule as RR;
use owoframe\utils\DataEncoder;

// 绑定域名 test.xxx.com 到 IndexApp;
RR::domain('xxx.com', ['test' => 'index']);

// 添加一个Api处理器;
RR::bindApiProcessor(new class extends owoframe\application\ApiProcessor
{
	public function getOutput() : array
	{
		$output = new DataEncoder(['msg' => 'Currently this processor appears to be working well~']);
		return $output->getOriginData();
	}

	public static function getName() : string
	{
		// The name for url to match;
		return 'test';
	}

	public static function getVersion() : string
	{
		// api version;
		return "v1.0.0";
	}

	public static function mode() : int
	{
		return -1; // For details, please see the comments of the parent class 
	}
});