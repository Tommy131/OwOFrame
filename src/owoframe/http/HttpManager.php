<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2021 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Tommy131)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	* GitHub: https://github.com/Tommy131

************************************************************************/

declare(strict_types=1);
namespace owoframe\http;

use owoframe\contract\{HTTPStatusCodeConstant, Manager};
use owoframe\helper\Helper;
use owoframe\http\route\Router;
use owoframe\utils\{Config, LogWriter};

class HttpManager implements HTTPStatusCodeConstant, Manager
{
	/* @string const 日志识别前缀 */
	public const LOG_PREFIX = 'CRF/BeforeRoute';
	/* @array 默认的用于过滤的正则表达式 */
	public const DEFAULT_XSS_FILTER =
	[
		"/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
		"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
		"/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
		// "/(\\\(|\\\)| |\s|!|@|#|\\\$|%|\\\^|&|\\\*|\\\-|_|\\\+|\\\=|\\\||)/isU",
		// "/[`~!@#$%^&*()_\-+=<>?:\\\"{}|,.\/;'\\[\]·~！#￥%……&*（）——\-+={}|《》？：“”【】、；‘'，。、]/im"
	];

	/* @Config 黑名单配置文件 */
	private static $ipList;
	/* @array 自定义的用于过滤的正则表达式 */
	public static $customFilter = [];


	/**
	 * @method      start
	 * @description 启动HttpManager
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @return      void
	 */
	public function start() : void
	{
		// TODO: 将ClientRequestFilter中的方法移植过来;
		if(self::isForeverBanned(Helper::getClientIp())) {
			LogWriter::write('[403] Client '.self::$currentIp.'\'s IP is in the blacklist, request deined.', self::LOG_PREFIX);
			self::setStatusCode(403);
			return;
		}
		LogWriter::write('[200@' . server('REQUEST_METHOD') . '] Client ' . Helper::getClientIp() . ' requested url [' . Router::getCompleteUrl() . ']', self::LOG_PREFIX);
	}

	/**
	 * @method      setStatusCode
	 * @description 设置HTTP状态码
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @param       int      $code 状态码
	 */
	public static function setStatusCode(int $code) : void
	{
		if(isset(self::HTTP_CODE[$code])) {
			header(((server('SERVER_PROTOCOL') !== null) ? server('SERVER_PROTOCOL') : 'HTTP/1.1') . " {$code} " . self::HTTP_CODE[$code], true, $code);
		}
	}

	/**
	 * @method      setXssFilter
	 * @description 设置自定义的XSS过滤器
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @param       array       $filter 正则过滤器组
	 */
	public static function setXssFilter(array $filter) : void
	{
		self::$customFilter = array_merge(self::$customFilter, $filter);
	}

	/**
	 * @method      xssFilter
	 * @description XSS跨站请求过滤
	 * @author      HanskiJay
	 * @doenIn      2021-02-07
	 * @param       string      $str         需要过滤的参数
	 * @param       string      $allowedHTML 允许的HTML标签 (e.g. "<a><b><div>" (将不会过滤这三个HTML标签))
	 * @return      void
	 */
	public static function xssFilter(string &$str, string $allowedHTML = null) : void
	{
		$str = preg_replace(array_merge(self::DEFAULT_XSS_FILTER, self::$customFilter), '', strip_tags($str, $allowedHTML));
	}

	/**
	 * @method      getMerge
	 * @description 返回整个的请求数据(默认返回原型)
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       bool           $useXssFilter 是否使用默认的XSS过滤函数
	 * @param       callable|null  callback      回调参数
	 * @return      array (开发者需注意在此返回参数时必须使回调参数返回数组)
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

	/**
	 * @method      isBanned
	 * @description 判断IP地址是否被带时间封禁
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @param       string      $ip IP地址
	 * @return      boolean
	 */
	public static function isBanned(string $ip) : bool
	{
		return self::ipList()->exists(base64_encode($ip));
	}

	/**
	 * @method      isForeverBanned
	 * @description 判断IP地址是否被永久封禁
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @param       string      $ip IP地址
	 * @return      boolean
	 */
	public static function isForeverBanned(string $ip) : bool
	{
		if(!self::isBanned($ip)) {
			return false;
		}
		return self::ipList()->get(base64_encode($ip).'.banTime') == true;
	}

	/**
	 * @method      ipList
	 * @description 返回黑名单配置文件实例
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @return      @Config
	 */
	public static function ipList() : Config
	{
		if(!self::$ipList instanceof Config) {
			self::$ipList = new Config(LOG_PATH . 'ipList.json');
		}
		return self::$ipList;
	}
}