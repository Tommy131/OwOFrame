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

use owoframe\object\{JSON, INI};
use owoframe\exception\OwOFrameException;
use owoframe\exception\ResourceNotFoundException;

class Language
{
	/**
	 * 语言包名
	 *
	 * @access private
	 * @var string
	 */
	private $package;

	/**
	 * 文件路径
	 *
	 * @access private
	 * @var string
	 */
	private $file;

	/**
	 * 语言标签
	 *
	 * @access private
	 * @var string
	 */
	private $lang;

	/**
	 * 语言包存放数组
	 *
	 * @access private
	 * @var array
	 */
	private $langPack;



	public function __construct(string $file, string $package, array $pack, string $lang = 'en-US')
	{
		if(!file_exists($file)) {
			throw new ResourceNotFoundException('LanguagePack', $file);
		}
		$this->package  = $package;
		$this->file     = $file;
		$this->lang     = $lang;
		$this->langPack = $pack;
		$this->update('', '');
	}

	/**
	 * 判断语言包里是否存在一个条目
	 *
	 * @author HanskiJay
	 * @since  2021-01-31
	 * @param  string      $tag 标签
	 * @return boolean
	 */
	public function exists(string $tag) : bool
	{
		return isset($this->langPack[$tag]);
	}

	/**
	 * 获取语言包条目
	 *
	 * @author HanskiJay
	 * @since  2021-01-31
	 * @param  string      $tag     标签
	 * @param  string      $default 默认返回值
	 * @return string
	 */
	public function get(string $tag, string $default = 'Language tag {%s} undefined') : string
	{
		return $this->langPack[$tag] ?? (preg_match("/\{\%s\}/i", $default) ? sprintf($default, $tag) : $default);
	}

	/**
	 * 更新语言包条目
	 *
	 * @author HanskiJay
	 * @since  2021-01-31
	 * @param  string      $tag  标签
	 * @param  string      $text 文字
	 * @return void
	 */
	public function update(string $tag, string $text) : void
	{
		if(!empty($tag) && !empty($text)) {
			$this->langPack[$tag] = $text;
		}
		switch(@end(explode('.', $this->file))) {
			case 'ini':
				$config = new INI($this->file, [], true);
			break;

			case 'json':
				$config = new JSON($this->file, [], true);
			break;

			default:
				throw new OwOFrameException('Unsupported file type of this Language Package!');
			break;
		}
		$config->setAll($this->langPack);
	}
}