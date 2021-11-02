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
namespace owoframe\http\route;

class UrlRule
{
	/* @constant 检查规则 */
	public const URL_CHECK_RULES =
	[
		'[onlyMixedLetters]'           => '/[a-zA-Z]+/imU',
		'[onlyUppercaseLetters]'       => '/[A-Z]+/imU',
		'[onlyLowerLetters]'           => '/[a-z]+/imU',
		'[onlyMixedLettersAndNumbers]' => '/[a-zA-Z0-9]+/imU',
	];

	/**
	 * 请求路由
	 *
	 * @access private
	 * @var String
	 */
	private $url;

	/**
	 * 路由匹配规则
	 *
	 * @access private
	 * @var array
	 */
	private $rules = [];



	public function __construct(string $url, array $rules = [])
	{
		$this->url   = $url;
		$this->rules = $rules;
	}


	/**
	 * 验证路由是否有效
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @return boolean
	 */
	public function checkValid() : bool
	{
		if(strlen($this->url) === 0) {
			return true;
		}
		$checkUrl = '/' . $this->url;
		return false;
	}


	/**
	 * 设置请求路由
	 *
	 * @param  string  $url
	 * @return UrlRule
	 */
	public function setUrl(string $url) : UrlRule
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param  array   $rules
	 * @return UrlRule
	 */
	public function setRules(array $rules) : UrlRule
	{
		$this->rules = $rules;
		return $this;
	}

	/**
	 * 魔术方法: 直接返回类内部变量
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function __get(string $name)
	{
		return isset($this->{$name}) ? $this->{$name} : null;
	}
}