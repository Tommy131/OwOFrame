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
namespace owoframe\event\system;

use owoframe\contract\StandardOutput;

class OutputEvent extends \owoframe\event\Event implements StandardOutput
{
	/* @string 输出内容 */
	protected $output;

	public function __construct(string $output = '')
	{
		$this->output = $output;
	}

	/**
	 * @method      setOutput
	 * @description 更新输出内容
	 * @author      HanskiJay
	 * @doenIn      2021-04-11
	 * @param       string      $str 输出内容
	 */
	public function setOutput(string $str) : void
	{
		$this->__construct($str);
	}

	/**
	 * @method      getOutput
	 * @description 返回输出内容
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @return      string
	 */
	public function getOutput() : string
	{
		return $this->output;
	}

	public function output(bool $autoClean = false) : void
	{
		echo $this->output;
		if($autoClean) {
			$this->output = '';
		}
	}
}