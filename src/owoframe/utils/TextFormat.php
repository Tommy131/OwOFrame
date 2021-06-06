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
namespace owoframe\utils;

class TextFormat
{
	/* @string 定义§的编码 */
	public const PREFIX = "\xc2\xa7";

	/* 定义标准颜色 | Define Standard Colors */
	/* @string 水色(亮蓝色) */
	public const AQUA = self::PREFIX . "0";
	/* @string 黑色 */
	public const BLACK = self::PREFIX . "1";
	/* @string 蓝色(标准色) */
	public const BLUE = self::PREFIX . "2";
	/* @string 金色 */
	public const GOLD = self::PREFIX . "3";
	/* @string 灰色 */
	public const GRAY = self::PREFIX . "4";
	/* @string 绿色 */
	public const GREEN = self::PREFIX . "5";
	/* @string 紫色 */
	public const PURPLE = self::PREFIX . "6";
	public const LILA   = self::PREFIX . "a6";
	/* @string 红色 */
	public const RED        = self::PREFIX . "7";
	public const LIGHT_RED  = self::PREFIX . "a7";
	public const STRONG_RED = self::PREFIX . "c7";
	/* @string 白色 */
	public const WHITE = self::PREFIX . "8";
	/* @string 黄色 */
	public const YELLOW        = self::PREFIX . "9";
	public const NORMAL_YELLOW = self::PREFIX . "a9";

	/* @string 加粗 */
	public const BOLD = self::PREFIX . "b";
	/* @string 斜体字 */
	public const ITALIC = self::PREFIX . "i";
	/* @string 重置颜色 */
	public const RESET = self::PREFIX . "r";
	/* @string 删除线 */
	public const DELETE_LINE = self::PREFIX . "s";
	/* @string 删除线(正规叫法) */
	public const STRIKETHROUGH = self::DELETE_LINE;
	/* @string 下划线 */
	public const UNDERLINE = self::PREFIX . "u";

	/**
	 * @method      split
	 * @description 分割字符串
	 * @author      HanskiJay
	 * @doneIn      2021-01-27
	 * @param       string      $str 传入的字符串
	 * @return      array
	 */
	public static function split(string $str) : array
	{
		return preg_split("/(" . self::PREFIX . "[0-9birsu])|(" . self::PREFIX . "[a6a7a9c7]+)/", $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	}

	/**
	 * @method      clean
	 * @description 转换颜色字符
	 * @author      HanskiJay
	 * @doneIn      2021-01-27
	 * @param       string      $str 传入的字符串
	 * @return      string
	 */
	public static function clean(string $str) : string
	{
		return str_replace(self::PREFIX, "", preg_replace("/" . self::PREFIX . "[0-9birsu]|" . self::PREFIX . "[a6a7a9c7]+/", "", $str));
	}

	/**
	 * @method      parse
	 * @description 解析传入普通字符转换成颜色字符
	 * @author      HanskiJay
	 * @doneIn      2021-01-27
	 * @param       string      $input 传入的字符串
	 * @return      string
	 */
	public static function parse(string $input) : string
	{
		$form   = "\033[38;5;%sm";
		$output = '';
		foreach(self::split($input) as $v) {
			switch($v)
			{
				// When did not match;
				default:
					$output .= $v;
				break;

				// Match Colors;
				case self::AQUA:
					$output .= sprintf($form, '51');
				break;

				case self::BLACK:
					$output .= sprintf($form, '16');
				break;

				case self::BLUE:
					$output .= sprintf($form, '6');
				break;

				case self::GOLD:
					$output .= sprintf($form, '226');
				break;

				case self::GRAY:
					$output .= sprintf($form, '8');
				break;

				case self::GREEN:
					$output .= sprintf($form, '46');
				break;

				case self::PURPLE:
					$output .= sprintf($form, '5');
				break;
				case self::LILA:
					$output .= sprintf($form, '13');
				break;

				case self::RED:
					$output .= sprintf($form, '1');
				break;
				case self::LIGHT_RED:
					$output .= sprintf($form, '9');
				break;
				case self::STRONG_RED:
					$output .= sprintf($form, '196');
				break;

				case self::WHITE:
					// $output .= sprintf($form, '7');
					$output .= sprintf($form, '15');
				break;

				case self::YELLOW:
					$output .= sprintf($form, '226');
				break;
				case self::NORMAL_YELLOW:
					$output .= sprintf($form, '190');
				break;

				// Match Symbols;
				case self::BOLD:
					$output .= "\033[1m";
				break;

				case self::ITALIC:
					$output .= "\033[3m";
				break;

				case self::RESET:
					$output .= "\033[0m";
				break;

				case self::DELETE_LINE:
				case self::STRIKETHROUGH:
					$output .= "\033[9m";
				break;

				case self::UNDERLINE:
					$output .= "\033[4m";
				break;

			}
		}
		return $output . "\033[0m";
	}

	/**
	 * @method      color
	 * @description 取色器
	 * @author      HanskiJay
	 * @doneIn      2021-01-27
	 * @param       int      $num 颜色编号
	 * @param       string   $str 传入的字符
	 * @return      string
	 */
	public static function color(string $str, int $num = 15) : string
	{
		// Maximal color range cannot bigger than 250;
		if($num > 250) $num = 15;
		return "\033[38;5;{$num}m{$str}\033[0m";
	}

	/**
	 * @method      background
	 * @description 背景取色器
	 * @author      HanskiJay
	 * @doneIn      2021-01-27
	 * @param       string   $str 传入的字符
	 * @param       int      $num 背景颜色编号
	 * @param       int      $num2 字体颜色编号
	 * @return      string
	 */
	public static function background(string $str, int $num = 40, int $num2 = 37) : string
	{
		// 40: 黑色  41: 红色 42: 绿色  43: 黄色   44: 蓝色  45: 紫色   46: 天蓝色 47: 白色;
		// 40: Black 41: Red 42: Green 43: Yellow 44: Blue 45: Purple 46: Auqa  47: White;
		// Color number should be in the range 40 ~ 47;
		if(($num > 47) || ($num < 40)) $num = 40;
		return "\033[{$num};{$num2}m{$str}\033[0m";
	}

	public static function sendClear() : void
	{
		echo "\033[2J\033[0m" . PHP_EOL;
	}
}