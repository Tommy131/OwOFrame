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
namespace owoframe\exception;

class OwOFrameException extends \Exception
{
	/**
	 * 获取真实文件位置
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return string
	 */
	public function getRealFile() : string
	{
		$trace = $this->getTrace();
		return $trace[1]['file'] ?? $trace[0]['file'];
	}

	/**
	 * 获取真实错误行数
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return string
	 */
	public function getRealLine() : int
	{
		$trace = $this->getTrace();
		return $trace[1]['line'] ?? $trace[0]['line'];
	}

	/**
	 * 获取真实错误方法
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return string
	 */
	public function getMethod() : string
	{
		$trace = $this->getTrace();
		return $trace[1]['function'] ?? $trace[0]['function'];
	}
}