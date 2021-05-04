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
use owoframe\http\Session;
use owoframe\http\route\Router;
use owoframe\object\JSON;
use owoframe\utils\LogWriter;

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
	protected static $ipList;
	/* @array 不记录日志的路由 */
	protected static $notLogUrl = [];
	/* @array 自定义的用于过滤的正则表达式 */
	public static $customFilter = [];


	/**
	 * @method      start
	 * @description 启动HttpManager
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @return      void
	 */
	public function start(bool $autoDispath = true) : void
	{
		// TODO: 将ClientRequestFilter中的方法移植过来;
		$ip = Helper::getClientIp();
		if(!self::isIpValid($ip)) {
			LogWriter::write('[403@Banned] Client ' . $ip . '\'s IP is banned, request deined.', self::LOG_PREFIX);
			self::setStatusCode(403);
			return;
		}
		if($autoDispath) {
			if(ob_get_level() === 0) ob_start();
			Session::start();
			Router::dispath();
		}
		if(!in_array(server('REQUEST_URI'), self::$notLogUrl)) LogWriter::write('[B200@' . server('REQUEST_METHOD') . '] ' . $ip . ' -> ' . self::getCompleteUrl(), self::LOG_PREFIX);
	}

	public static function pushInLogFilter(string $uri) : void
	{
		self::$notLogUrl[] = $uri;
	}


	/******************************
	 *
	 * HTTP 参数操作方法
	 * 
	******************************/
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
	 * @method      Response
	 * @description 快速新建响应头实例
	 * @author      HanskiJay
	 * @doenIn      2021-03-18
	 * @param       null|callable    $callback 可回调参数
	 * @param       array            $params   回调参数传递
	 * @param       bool             $reload   重新生成响应实例
	 */
	public static function Response(?callable $callback = null, array $params = [], bool $reload = false) : Response
	{
		static $response;

		if($reload) {
			$response = null;
		}
		if(!$response instanceof Response) {
			$response = new Response($callback, $params);
		}
		return $response;
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
	 * @method      getRequestMerge
	 * @description 返回整个的请求数据(默认返回原型)
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       bool           $useXssFilter 是否使用默认的XSS过滤函数
	 * @param       callable|null  callback      回调参数
	 * @return      array (开发者需注意在此返回参数时必须使回调参数返回数组)
	 */
	public static function getRequestMerge(bool $useXssFilter = true, ?callable $callback = null) : array
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


	/******************************
	 *
	 * ClientIp 操作方法
	 * 
	******************************/
	/**
	 * @method      banIp
	 * @description 封禁一个IP
	 * @author      HanskiJay
	 * @doenIn      2021-03-09
	 * @param       string      $ip     IP地址
	 * @param       int|integer $toTime 封禁到时间(默认10分钟)
	 * @param       string      $reason 封禁理由
	 * @return      void
	 */
	public static function banIp(string $ip, int $toTime = 10, string $reason = '') : void
	{
		if(Helper::isIp($ip)) {
			$encodedIp = base64_encode($ip);
		}
		$toTime = microtime(true) + $toTime * 60;
		if(!self::isBanned($ip)) {
			self::ipList()->set($encodedIp, 
			[
				'origin'  => $ip,
				'banTime' => $toTime,
				'reason'  => $reason
			]);
		} else {
			self::ipList()->set($encodedIp.'.banTime', $toTime);
		}
		self::ipList()->save();
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
		if(Helper::isIp($ip)) {
			$ip = base64_encode($ip);
		}
		$ipData = self::ipList()->get($ip);
		return ($ipData !== null) && isset($ipData['banTime']);
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
		if(Helper::isIp($ip)) {
			$ip = base64_encode($ip);
		}
		if(!self::isBanned($ip)) {
			return false;
		}
		return self::ipList()->get($ip.'.banTime') == true;
	}

	/**
	 * @method      setIpData
	 * @description 设置IP信息集
	 * @author      HanskiJay
	 * @doenIn      2021-03-09
	 * @param       string      $ip   IP地址
	 * @param       array       $data 自定义设置信息集
	 * @return      object@JSON
	 */
	public static function setIpData(string $ip, array $data) : JSON
	{
		if(Helper::isIp($ip)) {
			$encodedIp = base64_encode($ip);
		}
		$ipData    = self::ipList()->get($encodedIp) ?? [];
		$ipData    = array_merge($ipData, $data);
		if(!isset($ipData['origin'])) {
			$ipData['origin'] = $ip;
		}
		self::ipList()->set($encodedIp, $ipData);
		self::ipList()->save();
		return self::ipList();
	}

	/**
	 * @method      isIpValid
	 * @description 判断当前IP的访问有效性
	 * @author      HanskiJay
	 * @doenIn      2021-03-13
	 * @param       string      $ip IP地址
	 * @return      boolean
	 */
	private static function isIpValid(string $ip) : bool
	{
		if(Helper::isIp($ip)) {
			$encodedIp = base64_encode($ip);
		}
		if(!self::isBanned($ip)) {
			return true;
		}
		if(self::isForeverBanned($ip) || (microtime(true) - self::ipList()->get($encodedIp.'.banTime') > 0)) {
			return false;
		}
	}

	/**
	 * @method      ipList
	 * @description 返回黑名单配置文件实例
	 * @author      HanskiJay
	 * @doenIn      2021-03-07
	 * @return      object@JSON
	 */
	public static function ipList() : JSON
	{
		if(!self::$ipList instanceof JSON) {
			self::$ipList = new JSON(FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'ipList.json');
		}
		return self::$ipList;
	}


	/******************************
	 *
	 * URI/URL 方法
	 * 
	******************************/
	/**
	 * @method      isSecure
	 * @description 判断是否为HTTPS协议
	 * @description Check if HTTP Protocol has used SSL
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function isSecure() : bool
	{
		return (!empty(server('HTTPS')) && 'off' != strtolower(server('HTTPS'))) 
			|| (!empty(server('SERVER_PORT')) && 443 == server('SERVER_PORT'));
	}

	/**
	 * @method      getCompleteUrl
	 * @description 获取完整请求HTTP地址
	 * @description Get complete http requested url
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getCompleteUrl() : string
	{
		return server('REQUEST_SCHEME').'://'.server('HTTP_HOST').server('REQUEST_URI');
	}

	/**
	 * @method      getRootUrl
	 * @description 获取根地址
	 * @description Get root url
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getRootUrl() : string
	{
		return server('REQUEST_SCHEME').'://'.server('HTTP_HOST');
	}

	/**
	 * @method      betterUrl
	 * @description 返回自定义Url
	 * @description Set HTTP_HEADER;
	 * @param       string[name|名称]
	 * @param       string[path|路径]
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function betterUrl(string $name, string $path) : string
	{
		return trim($path, '/').'/'.str_replace('//', '/', ltrim(((0 === strpos($name, './')) ? substr($name, 2) : $name), '/'));
	}
}