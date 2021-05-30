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

class MethodMissedException extends OwOFrameException
{
	/* @callable 备选回调方法 */
	private $alternativeCall = [];
	/* @bool 判断条件结果 */
	private $judgement = true;


	public function __construct(string $className, string $method, int $code = 0, \Throwable $previous = null)
	{
		parent::__construct("Called an undefined method [{$className}::{$method}]!", $code, $previous);
	}

	/**
	 * @method      setAlternativeCall
	 * @description 设置备选回调方法
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @param       callable           $callback
	 */
	public function setAlternativeCall(callable $callback) : void
	{
		$this->alternativeCall = $callback;
	}

	/**
	 * @method      getAlternativeCall
	 * @description 获取备选回调方法
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @return      null|callable
	 */
	public function getAlternativeCall() : ?callable
	{
		return $this->alternativeCall;
	}

	/**
	 * @method      setJudgement
	 * @description 设置判断条件结果
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @param       bool         $judgement 判断条件结果
	 */
	public function setJudgement(bool $judgement = true) : void
	{
		$this->judgement = $judgement;
	}

	/**
	 * @method      getJudgement
	 * @description 获取判断条件结果
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @return      boolean
	 */
	public function getJudgement() : bool
	{
		return $this->judgement;
	}

	/**
	 * @method      resetCall
	 * @description 重置回调方法相关
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 */
	public function resetCall() : void
	{
		$this->alternativeCall = null;
		$this->judgement       = true;
	}

	/**
	 * @method      toggleRunTimeDivOutput
	 * @description 切换输出运行时间框
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @param       bool|boolean           $update 更新输出状态
	 * @return      boolean
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