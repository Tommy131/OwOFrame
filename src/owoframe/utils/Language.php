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

namespace owoframe\utils;

use owoframe\exception\ResourceMissedException;

class Language
{
	/* @string 语言包名 */
	private $package;
	/* @string 文件路径 */
	private $file;
	/* @string 语言标签 */
	private $lang;
	/* @array 语言包存放数组 */
	private $langPack;

	public function __construct(string $package, array $pack, string $lang = 'en-US')
	{
		if(!file_exists($file)) {
			throw new ResourceMissedException('LanguagePack', $file);
		}
		$this->package  = $package;
		$this->file     = $file;
		$this->lang     = $lang;
		$this->langPack = $pack;
	}

	/**
	 * @method      exists
	 * @description 判断语言包里是否存在一个条目
	 * @author      HanskiJay
	 * @doenIn      2021-01-31
	 * @param       string      $tag 标签
	 * @return      boolean
	 */
	public function exists(string $tag) : bool
	{
		return isset($this->langPack[$tag]);
	}

	/**
	 * @method      get
	 * @description 获取语言包条目
	 * @author      HanskiJay
	 * @doenIn      2021-01-31
	 * @param       string      $tag     标签
	 * @param       string      $default 默认返回值
	 * @return      string
	 */
	public function get(string $tag, string $default = 'Language tag {%s} undefined') : string
	{
		return $this->langPack[$tag] ?? (preg_match("/\{\%s\}/i", $default)? sprintf($default, $tag) : $default);
	}

	/**
	 * @method      update
	 * @description 更新语言包条目
	 * @author      HanskiJay
	 * @doenIn      2021-01-31
	 * @param       string      $tag  标签
	 * @param       string      $text 文字
	 * @return      void
	 */
	public function update(string $tag, string $text) : void
	{
		$this->langPack[$tag] = $text;
		switch(@end(explode('.', $this->file))) {
			case 'ini':
				$r = "[{$this->lang}]" . PHP_EOL;
				foreach($this->langPack as $tag => $text) {
					$r .= "{$tag}={$text}" . PHP_EOL;
				}
				file_put_contents($this->file, $r);
			break;

			case 'json':
				$config = new Config($this->file, [], true);
				$config->setAll($this->langPack);
			break;
		}
	}
}