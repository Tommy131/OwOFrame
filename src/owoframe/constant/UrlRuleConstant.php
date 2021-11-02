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

interface UrlRuleConstant
{
	/**
	 * 仅匹配混合字母
	 */
	public const TAG_ONLY_MIXED_LETTERS = '[onlyMixedLetters]';
	/**
	 * 仅匹配大写字母
	 */
	public const TAG_ONLY_UPPERCASE_LETTERS = '[onlyUppercaseLetters]';
	/**
	 * 仅匹配小写字母
	 */
	public const TAG_ONLY_LOWERCASE_LETTERS = '[onlyLowerLetters]';
	/**
	 * 仅匹配混合字母与数字
	 */
	public const TAG_ONLY_MIXED_LETTERS_AND_NUMBERS = '[onlyMixedLettersAndNumbers]';
	/**
	 * 仅匹配普通字符串;
	 */
	public const TAG_NORMAL_CHARACTER = '[normalCharacter]';
	/**
	 * 仅匹配数字
	 */
	public const TAG_ONLY_NUMBERS = '[onlyNumbers]';
	/**
	 * 仅匹配Get格式: /?param1=1&param2=2
	 */
	public const TAG_ONLY_GET_STYLE = '[onlyGetStyle]';
	/**
	 * 使用默认匹配格式
	 */
	public const TAG_USE_DEFAULT_STYLE = '[useDefaultStyle]';

	/**
	 * 规则定义
	 */
	public const URL_CHECK_RULES =
	[
		self::TAG_ONLY_MIXED_LETTERS             => '/[a-zA-Z]+/U',
		self::TAG_ONLY_UPPERCASE_LETTERS         => '/[A-Z]+/U',
		self::TAG_ONLY_LOWERCASE_LETTERS         => '/[a-z]+/U',
		self::TAG_ONLY_MIXED_LETTERS_AND_NUMBERS => '/[a-zA-Z0-9]+/U',
		self::TAG_NORMAL_CHARACTER               => '/\w+/U',
		self::TAG_ONLY_NUMBERS                   => '/[0-9]+/U',
		self::TAG_ONLY_GET_STYLE                 => '/(\w+=\w+)(.*)/U',
		self::TAG_USE_DEFAULT_STYLE              => '/(\w+)/U'
	];
}