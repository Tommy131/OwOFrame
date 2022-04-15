<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2019 OwOBlog-DGMT.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay
	* E-Mail: support@owoblog.com
	*
	* 此配置文件为域名绑定规则的配置文件.
	* This configuration is for bind domain(s) to application.

************************************************************************/
use owoframe\http\route\UrlRule;
use owoframe\http\route\DomainRule as Domain;


// * 绑定域名到指定的应用程序;
// Domain::bind('example.com', Domain::TAG_BIND_TO_APPLICATION, 'appName');
// * 绑定域名到指定的URL地址; (一经绑定则此域名仅可以访问此URL地址!!!)
// Domain::bind('example.com', Domain::TAG_BIND_TO_URL, 'https:://{domain}/appName/...');


/**
 * @method $customizeUrlRule
 *
 * * 自定义路由规则闭包方法 (变量名不能更改为其他的名字!!!)
 *
 * @param   string $pathInfo
 * @example 取消下方注释以进行使用
 */
/*
$customizeUrlRule = function(string $restPath) : UrlRule
{
	return new UrlRule($restPath, UrlRule::TAG_USE_DEFAULT_STYLE);
};
*/