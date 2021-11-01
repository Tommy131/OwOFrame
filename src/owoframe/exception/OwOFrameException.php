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
	/* @callable 备选回调方法 */
	private $alternativeCall = [];
	/* @bool 判断条件结果 */
	private $judgement = true;




	/**
	 * 获取真实文件位置
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return string
	 */
	public function getRealFile() : string
	{
		return $this->getTrace()[1]['file'] ?? $this->getTrace()[0]['file'];
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
		return $this->getTrace()[1]['line'] ?? $this->getTrace()[0]['line'];
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
		return $this->getTrace()[1]['function'] ?? $this->getTrace()[0]['function'];
	}

	/**
	 * 设置备选回调方法
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @param  callable      $callback
	 */
	public function setAlternativeCall(callable $callback) : void
	{
		$this->alternativeCall = $callback;
	}

	/**
	 * 获取备选回调方法
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return null|callable
	 */
	public function getAlternativeCall() : ?callable
	{
		return $this->alternativeCall ?? null;
	}

	/**
	 * 设置判断条件结果
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @param  bool      $judgement 判断条件结果
	 */
	public function setJudgement(bool $judgement = true) : void
	{
		$this->judgement = $judgement;
	}

	/**
	 * 获取判断条件结果
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @return boolean
	 */
	public function getJudgement() : bool
	{
		return $this->judgement;
	}

	/**
	 * 重置回调方法相关
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 */
	public function resetCall() : void
	{
		$this->alternativeCall = null;
		$this->judgement       = true;
	}

	/**
	 * 切换输出运行时间框
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @param  bool|boolean           $update 更新输出状态
	 * @return boolean
	 */
	public static function toggleRunTimeDivOutput(bool $update = true) : bool
	{
		static $status;
		if(!isset($status)) {
			$status = true;
		}
		if($update) {
			$status = ($status ? false : true);
		}
		return $status;
	}
}