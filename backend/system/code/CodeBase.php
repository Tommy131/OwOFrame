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
	
************************************************************************/

declare(strict_types=1);
namespace backend\system\code;

interface CodeBase
{
	# System Basic Status Code #

	/* @int 访问被拒绝状态码 */
	public const CODE_ACCESS_DENIED = 403;
	/* @int 服务器内部错误状态码 */
	public const CODE_SERVER_INTERVAL_ERROR = 500;

	# HTTP Request Mode Code #
	
	/* @int HTTP GET请求模式 */
	public const CODE_GET_MODE = 0;
	/* @int HTTP POST请求模式 */
	public const CODE_POST_MODE = 1;
	/* @int HTTP PUT请求模式 */
	public const CODE_PUT_MODE = 2;
	/* @int HTTP AJAX请求模式 */
	public const CODE_AJAX_MODE = 3;
	/* @int HTTP AJAX + GET请求模式 */
	public const CODE_AJAX_P_GET_MODE = 4;
	/* @int HTTP AJAX + POST请求模式 */
	public const CODE_AJAX_P_POST_MODE = 5;


	/*
	 * @User Module
	*/
	/* @int 用户成功登录状态码 */
	public const CODE_USER_LOGGED_IN_SUCCESSFULLY  = 200;
	/* @int 用户已登录状态码 */
	public const CODE_USER_HAS_LOGGED_IN = 201;
	/* @int 用户密码未验证状态码 */
	public const CODE_USER_PASSWORD_NOT_VERIFIED = 400;
	/* @int 用户操作状态码: 验证码错误 */
	public const CODE_VERIFY_CODE_INCORRECT = 401;
	/* @int 用户操作状态码: 密码错误 */
	public const CODE_USER_PASSWORD_INCORRECT = 402;
	/* @int 用户操作状态码: 账号访问被冻结 */
	public const CODE_USER_ACCESS_DENIED = 403;
	/* @int 用户操作状态码: 用户信息未找到 */
	public const CODE_USER_NOT_FOUND = 404;
	/* @int 用户操作状态码: 账号被封禁 */
	public const CODE_USER_WAS_BANNED = 10403;
}