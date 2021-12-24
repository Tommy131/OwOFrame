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

interface UserConstant
{

	/**
	 * 在线状态
	 */
	public const STATUS_ONLINE = 0x0a0;

	/**
	 * 隐身状态
	 */
	public const STATUS_STEALTH = 0x0a1;

	/**
	 * 忙碌状态
	 */
	public const STATUS_BUSY = 0x0a2;

	/**
	 * 离开状态
	 */
	public const STATUS_GO_AWAY = 0x0a3;

	/**
	 * 勿扰状态
	 */
	public const STATUS_NOT_DISTURB = 0x0a4;

	/**
	 * 离线状态
	 */
	public const STATUS_OFFLINE = 0x0a5;

	/**
	  * 用户状态标签 | User Status Labels
	*/
	public const STATUS_LABELS =
	[
		self::STATUS_ONLINE      => 'online',
		self::STATUS_STEALTH     => 'stealth',
		self::STATUS_BUSY        => 'busy',
		self::STATUS_GO_AWAY     => 'go-away',
		self::STATUS_NOT_DISTURB => 'do-not-disturb',
		self::STATUS_OFFLINE     => 'offline',
	];
}