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
namespace owoframe\constant;

interface BasicConstant
{
	# System Basic Status Code #

	/**
	 * 操作成功
	 */
	public const SUCCESS = 200;

	/**
	 * 访问被拒绝状态码
	 */
	public const ACCESS_DENIED = 403;

	/**
	 * 服务器内部错误状态码
	 */
	public const SERVER_INTERVAL_ERROR = 500;


	# HTTP Request Mode Code #

	/**
	 * HTTP GET请求模式
	 */
	public const GET_MODE = 0;

	/**
	 * HTTP POST请求模式
	 */
	public const POST_MODE = 1;

	/**
	 * HTTP PUT请求模式
	 */
	public const PUT_MODE = 2;

	/**
	 * HTTP AJAX请求模式
	 */
	public const AJAX_MODE = 3;

	/**
	 * HTTP AJAX + GET请求模式
	 */
	public const AJAX_P_GET_MODE = 4;

	/**
	 *  HTTP AJAX + POST请求模式
	 */
	public const AJAX_P_POST_MODE = 5;
}