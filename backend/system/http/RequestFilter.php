<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	
************************************************************************/

namespace backend\system\http;

use backend\OwOFrame;
use backend\system\exception\JSONException;

class RequestFilter
{
	/* @array 默认的用于过滤的正则表达式 */
	private static $defaultFilter =
	[
		"/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
		"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
		"/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
		// "/(\\\(|\\\)| |\s|!|@|#|\\\$|%|\\\^|&|\\\*|\\\-|_|\\\+|\\\=|\\\||)/isU",
		// "/[`~!@#$%^&*()_\-+=<>?:\\\"{}|,.\/;'\\[\]·~！#￥%……&*（）——\-+={}|《》？：“”【】、；‘'，。、]/im"
	];
	/* @array 自定义的用于过滤的正则表达式 */
	public static $customFilter = [];

	/**
	 * @method      xssFilter
	 * @description XSS跨站请求过滤
	 * @author      HanskiJay
	 * @doenIn      2021-02-07
	 * @param       string[str|需要过滤的参数]
	 * @param       string[allowedHTML|允许的HTML标签] e.g. "<a><b><div>" (将不会过滤这三个HTML标签)
	 * @return      void
	 */
	public static function xssFilter(string &$str, string $allowedHTML = null) : void
	{
		$str = preg_replace(array_merge(self::$defaultFilter, self::$customFilter), '', strip_tags($str, $allowedHTML));
	}

	/**
	 * @method      getMerge
	 * @description 返回整个的请求数据(默认返回原型)
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       bool[useXssFilter|是否使用默认的XSS过滤函数(Default: true)]
	 * @param       callable|null[callback|回调参数]
	 * @return      array(开发者需注意在此返回参数时必须使回调参数返回数组)
	 */
	public static function getMerge(bool $useXssFilter = true, ?callable $callback = null) : array
	{
		if($useXssFilter) {
			$get = $post = [];
			foreach(get(owohttp) as $k => $v) {
				$k = trim($k);
				$v = trim($v);
				self::xssFilter($k);
				self::xssFilter($v);
				$get[$k] = $v;
			}
			foreach(post(owohttp) as $k => $v) {
				$k = trim($k);
				$v = trim($v);
				self::xssFilter($k);
				self::xssFilter($v);
				$post[$k] = $v;
			}
			$array = ['get' => $get, 'post' => $post];
		} else {
			$array = ['get' => get(owohttp), 'post' => post(owohttp)];
		}
		return !is_null($callback) ? call_user_func_array($callback, $array) : $array;
	}
}