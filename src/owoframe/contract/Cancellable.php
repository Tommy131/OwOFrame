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

// This file is for EventManager to recognize which event is cancelable;
// Cancellable: American English | Cancelable: British English;
interface Cancellable
{
	/**
	 * @method      isCancelled
	 * @description 返回该事件是否已经频闭回调;
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @return      boolean
	 */
	public function isCancelled() : bool;

	/**
	 * @method      setCancelled
	 * @description 设置事件频闭状态
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @param       bool        $status 频闭状态
	 */
	public function setCancelled(bool $status) : void;
}