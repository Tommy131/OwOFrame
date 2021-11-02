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

use owoframe\constant\UrlRuleConstant;
use owoframe\exception\ParameterTypeErrorException;

class UrlRule implements UrlRuleConstant
{
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
	 * @var string
	 */
	private $rule;



	public function __construct(string $url, string $rule)
	{
		$this->url  = $url;
		$this->rule = $rule;
	}


	/**
	 * 验证路由是否有效
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  array             &$params   从URL取得的参数
	 * @param  null|callable      $callback 回调方法, 可自定义检查URL的合法性 [传入参数到回调方法: 匹配规则 | URL地址 | URL解析数组]
	 * @return boolean
	 */
	public function checkValid(&$params = [], ?callable $callback = null) : bool
	{
		// Prevent empty URL;
		if(strlen($this->url) === 0) {
			return true;
		}
		$parsed = parse_url('/' . $this->url);

		// Check whether thr rule is in the list;
		if(isset(self::URL_CHECK_RULES[$this->rule])) {
			$rule = self::URL_CHECK_RULES[$this->rule];
			// Cycle check the path;
			$paths = array_filter(explode('/', $parsed['path']));
			foreach($paths as $path) {
				if(!(bool) preg_match($rule, $path)) {
					return false;
				}
			}
			$params['restPath'] = $paths;

			if(isset($parsed['query'])) {
				$params['get'] = [];
				// Parse the rest query from the URL;
				$queries = array_filter(explode('=', $parsed['query']));
				foreach($queries as $name => $value) {
					$params['get'][$name] = $value;
				}
				// Merge the both GET data from HTTP_REQUEST;
				$params['get'] = array_merge($params['get'], $_GET);
			}
			return true;
		} else {
			// Check the custom regex validity;
			if(!(bool) preg_match('/^\/.*\/(\w+)?$/m', $this->rule)) {
				throw new ParameterTypeErrorException('$rule', 'regex', '');
			}
			// If the custom regex matched the URL;
			if((bool) preg_match($this->rule, $this->url)) {
				if(is_callable($callback)) {
					return call_user_func_array($callback, [$this->rule, $this->url, $parsed]);
				} else {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 通过分解路径单一检查
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  null|string      $rule
	 * @see   类型可参考写法: /[onlyNumbers]/[onlyLowercaseLetters]/...
	 * @see   具体标签代表请参考\owoframe\constant\UrlRuleConstant
	 * @return boolean
	 */
	public function checkWithSeparate(?string $rule = null) : bool
	{
		// Use Regex to grab effective rules;
		if(!(bool) preg_match_all('/\[\w+\]{1,}/iU', $rule ?? $this->rule, $rules)) {
			return false;
		}
		$rules = array_shift($rules);

		// Separate UrlPath;
		$paths = array_filter(explode('/', '/' . $this->url));
		if(count($rules) !== count($paths)) {
			return false;
		}

		// Start to check;
		$start = 0;
		foreach($paths as $path) {
			if(!isset(self::URL_CHECK_RULES[$rules[$start]])) {
				return false;
			}
			$rule = self::URL_CHECK_RULES[$rules[$start]];

			if(!(bool) preg_match($rule, $path)) {
				return false;
			}
			++$start;
		}
		return true;
	}

	/**
	 * 设置请求路由
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string      $url
	 * @return UrlRule
	 */
	public function setUrl(string $url) : UrlRule
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * 设置路由规则
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string      $rule
	 * @return UrlRule
	 */
	public function setRule(string $rule) : UrlRule
	{
		$this->rule = $rule;
		return $this;
	}

	/**
	 * 魔术方法: 直接返回类内部变量
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string      $name
	 * @return mixed
	 */
	public function __get(string $name)
	{
		return isset($this->{$name}) ? $this->{$name} : null;
	}
}