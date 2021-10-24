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

declare(strict_types=1);
namespace owoframe\contract;

interface BasicCodes
{
	# System Basic Status Code #
	/* @int 操作成功 */
	public const SUCCESS = 200;
	/* @int 访问被拒绝状态码 */
	public const ACCESS_DENIED = 403;
	/* @int 服务器内部错误状态码 */
	public const SERVER_INTERVAL_ERROR = 500;
	/* @int 通用状态码: 非法名称 */
	public const ILLEGAL_NAME = 19000;
	/* @int 通用状态码: 验证码错误 */
	public const VERIFY_CODE_INCORRECT = 19001;


	# HTTP Request Mode Code #
	/* @int HTTP GET请求模式 */
	public const GET_MODE = 0;
	/* @int HTTP POST请求模式 */
	public const POST_MODE = 1;
	/* @int HTTP PUT请求模式 */
	public const PUT_MODE = 2;
	/* @int HTTP AJAX请求模式 */
	public const AJAX_MODE = 3;
	/* @int HTTP AJAX + GET请求模式 */
	public const AJAX_P_GET_MODE = 4;
	/* @int HTTP AJAX + POST请求模式 */
	public const AJAX_P_POST_MODE = 5;


	# @User Module - 10xxx #
	/* @int 注册操作状态码: 用户已存在 */
	public const USER_EXISTS = 10100;
	/* @int 状态码: 用户成功登录 */
	public const USER_LOGGED_IN_SUCCESSFULLY  = 10200;
	/* @int 状态码: 用户已登录 */
	public const USER_HAS_LOGGED_IN = 10201;
	/* @int 状态码: 用户已登出 */
	public const USER_LOGGED_OUT_SUCCESSFULLY = 10202;
	/* @int 状态码: 用户密码未验证 */
	public const USER_PASSWORD_NOT_VERIFIED = 10400;
	/* @int 用户操作状态码: 密码错误 */
	public const USER_PASSWORD_INCORRECT = 10401;
	/* @int 用户操作状态码: 账号访问被冻结 */
	public const USER_ACCESS_DENIED = 10402;
	/* @int 用户操作状态码: 账号被封禁 */
	public const USER_WAS_BANNED = 10403;
	/* @int 用户操作状态码: 用户不存在或信息未找到 */
	public const USER_NOT_FOUND = 10404;
	/* @int 用户操作状态码: 用户未登录 */
	public const USER_HAS_NOT_LOGGED_IN = 10405;
	/* @int 用户操作状态码: 无效的用户数据 */
	public const INVALID_USERDATA = 10406;
}