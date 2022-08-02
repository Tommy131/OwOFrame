<?php

/************************************************************************
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
	*
	* 杂项方法公共存放类

************************************************************************/

declare(strict_types=1);
namespace owoframe\helper;

use owoframe\constants\HTTPConstant;
use owoframe\constants\MIMETypeConstant;

class Helper implements HTTPConstant, MIMETypeConstant
{

	/**
	 * Android系统标识
	 */
	public const OS_ANDROID = 'android';

	/**
	 * Linux系统标识
	 */
	public const OS_LINUX   = 'linux';

	/**
	 * Windows系统标识
	 */
	public const OS_WINDOWS = 'windows';

	/**
	 * Mac系统标识
	 */
	public const OS_MACOS   = 'mac';

	/**
	 * BSD系统标识
	 */
	public const OS_BSD     = 'bsd';

	/**
	 * 未识别的系统标识
	 */
	public const OS_UNKNOWN = 'unknown';


	/**
	 * 获取客户端信息
	 *
	 * @author HanskiJay
	 * @since  2021-01-10
	 * @return string
	 */
	public static function getClientBrowser() : string
	{
		if(!empty(server('HTTP_USER_AGENT')))
		{
			$br = server('HTTP_USER_AGENT');
			if(preg_match('/MSIE/i',$br))        $br = 'MSIE';
			elseif(preg_match('/Firefox/i',$br)) $br = 'Firefox';
			elseif(preg_match('/Chrome/i',$br))  $br = 'Chrome';
			elseif(preg_match('/Safari/i',$br))  $br = 'Safari';
			elseif(preg_match('/Opera/i',$br))   $br = 'Opera';
			else $br = 'Other';
			return $br;
		}
		else return "获取浏览器信息失败！";
	}

	/**
	 * 获取客户端IP
	 *
	 * @author HanskiJay
	 * @since  2021-01-10
	 * @return string
	 */
	public static function getClientIp() : string
	{
		if(getenv("HTTP_CLIENT_IP"))           $ip = getenv("HTTP_CLIENT_IP");
		elseif(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		elseif(getenv("REMOTE_ADDR"))          $ip = getenv("REMOTE_ADDR");
		else                                   $ip = "Unknown";
		return $ip;
	}

	/**
	 * 返回当前系统类型
	 *
	 * @author HanskiJay
	 * @since  2021-02-18
	 * @return string
	 */
	public static function getOS() : string
	{
		$r  = null;
		$os = php_uname('s');
		if(stripos($os, 'linux') !== false) {
			$r = @file_exists('/system/build.prop') ? self::OS_ANDROID : self::OS_LINUX;
		}
		elseif(stripos($os, 'windows') !== false) {
			$r = self::OS_WINDOWS;
		}
		elseif((stripos($os, 'mac') !== false) || (stripos($os, 'darwin') !== false)) {
			$r = self::OS_MACOS;
		}
		elseif(stripos($os, 'bsd') !== false) {
			$r = self::OS_BSD;
		}
		return $r ?? self::OS_UNKNOWN;
	}

	/**
	 * 获取所有的Mime类型
	 *
	 * @author HanskiJay
	 * @return array
	 */
	public static function getMimeType() : array
	{
		return self::MIMETYPE;
	}

	/**
	 * 检测是否为移动设备访问
	 *
	 * @author HanskiJay
	 * @since  2021-01-10
	 * @return boolean
	 */
	public static function isMobile(&$agent = null) : bool
	{
		//获取USER AGENT
		$agent = server('HTTP_USER_AGENT');

		return (bool) preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $agent);
	}

	/**
	 * 判断传入的字符串是否为有效IP地址
	 *
	 * @author HanskiJay
	 * @since  2020-10-24
	 * @param  string      $ip 字符串
	 * @return boolean
	 */
	public static function isIp(string $ip) : bool
	{
		return (bool) preg_match("/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/", $ip);
	}

	/**
	 * 判断字符串是否为Url地址
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string      $str 字符串
	 * @return boolean
	 */
	public static function isUrl(string $str) : bool
	{
		return (bool) preg_match('/^((http|https):\/\/)?\w+\.\w+\//iU', $str);
	}

	/**
	 * 检查是否是一个安全的主机名
	 *
	 * @author *
	 * @param  string      $host 主机名
	 * @return boolean
	 */
	public static function isSafeHost(string $host) : bool
	{
		if($host === 'localhost') return false;

		$address = gethostbyname($host);
		$inet    = inet_pton($address);

		if($inet === false) {
			// 有可能是ipv6的地址;
			// @see https://www.php.net/manual/zh/function.dns-get-record.php
			$records = dns_get_record($host, DNS_AAAA);
			if(empty($records)) return false;
			$address = $records[0]['ipv6'];
			$inet = inet_pton($address);
		}

		if(strpos($address, '.') !== false)
		{
			// ipv4, 非公网地址;
			$privateNetworks =
			[
				'10.0.0.0|10.255.255.255',
				'172.16.0.0|172.31.255.255',
				'192.168.0.0|192.168.255.255',
				'169.254.0.0|169.254.255.255',
				'127.0.0.0|127.255.255.255'
			];
			$long = ip2long($address);

			foreach($privateNetworks as $network) {
				[$from, $to] = explode('|', $network);
				if($long >= ip2long($from) && $long <= ip2long($to)) return false;
			}
		} else {
			// ipv6, https://en.wikipedia.org/wiki/Private_network;
			$from = inet_pton('fd00::');
			$to = inet_pton('fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
			if($inet >= $from && $inet <= $to) return false;
		}
		return true;
	}

	/**
	 * 判断字符串是否为多级域名格式
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string $str    字符串
	 * @param  array  &$match 允许的一级域名
	 * @return boolean
	 */
	public static function isDomain(string $str, array &$match = ['localhost']) : bool
	{
		$str = str_replace(['http', 'https', '://'], '', trim($str));
		if(in_array($str, $match)) {
			$match = $str;
			return true;
		}
		// * Regex verified: https://regex101.com/r/rhSD1e/1;
		return (strpos($str, '--') === false) && (bool) preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?[\.]*)+?([a-z]+)$/i', $str, $match);
	}

	/**
	 * 简单判断字符串是否为邮箱格式
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string  $str
	 * @param  string   &$suffix 允许匹配的域名后缀 (e.g.: $suffix = 'com.com.cn|abc.cn'), 匹配完成后传入匹配结果到此参数
	 * @return boolean
	 */
	public static function isEmail(string $str, string &$suffix = '') : bool
	{
		$preset = 'com|org|net|com.cn|org.cn|net.cn|cn';
		// Judgement for the allowed suffix format;
		if(preg_match('/[a-z.|]+/i', $suffix)) {
			$preset .= '|' . $suffix;
		}
		$preset = str_replace('.', '\.', $preset);
		return (bool) preg_match('/^([\w+\-.]+)@([a-z0-9\-.]+)\.(' . $preset . ')$/i', trim($str), $suffix);
	}

	/**
	 * 获取文件类型
	 *
	 * @author *
	 * @param  string      $fileName 文件名
	 * @return string
	 */
	public static function mimeContentType(string $fileName) : string
	{
		if(function_exists('mime_content_type')) {
			return mime_content_type($fileName);
		}

		if(function_exists('finfo_open'))
		{
			$fInfo = @finfo_open(FILEINFO_MIME_TYPE);
			if($fInfo !== false)
			{
				$mimeType = finfo_file($fInfo, $fileName);
				finfo_close($fInfo);
				return $mimeType;
			}
		}

		$part = explode('.', $fileName);
		$size = count($part);
		if($size > 1) {
			$ext = $part[$size - 1];
			if(isset(self::MIMETYPE[$ext])) return self::MIMETYPE[$ext];
		}
		return 'application/octet-stream';
	}

	/**
	 * 生成随机字符串
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  integer $length       生成长度
	 * @param  boolean $specialChars 是否加入符号
	 * @return string
	 */
	public static function randomString(int $length, bool $specialChars = false) : string
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if($specialChars) {
			$chars .= '!@#$%^&*()';
		}

		$result = '';
		$max = strlen($chars) - 1; // 字符串指针从0开始, 也就是长度[$length - 1]；
		for($i = 0; $i < $length; $i++) {
			$result .= $chars[rand(0, $max)];
		}
		return $result;
	}

	/**
	 * 创建一个随机的UUID
	 *
	 * @author HanskiJay
	 * @since  2022-01-08
	 * @return string
	 */
	public static function generateUUID() : string
	{
		$str   = md5(uniqid(self::randomString(5), true));
		$uuid  = '';
		$array = [0, 8, 12, 16, 20];
		foreach([8, 4, 4, 4, 12] as $k => $v) {
			$uuid .= substr($str, $array[$k], $v) . '-';
		}
		$uuid = rtrim($uuid, '-');
		return $uuid;
	}

	/**
	 * 删除文件夹
	 *
	 * @author HanskiJay
	 * @since  2021-04-17
	 * @param  string      $path 文件夹路径
	 * @return boolean
	 */
	public static function removeDir(string $path) : bool
	{
		if(!is_dir($path)) return false;
		$path = $path . DIRECTORY_SEPARATOR;
		$dirArray = scandir($path);
		unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);

		foreach($dirArray as $fileName) {
			if(is_dir($path . $fileName)) {
				self::removeDir($path . $fileName);
				if(is_dir($path . $fileName)) {
					rmdir($path . $fileName);
				}
			} else {
				unlink($path . $fileName);
			}
		}
		rmdir($path);
		return is_dir($path);
	}

	/**
	 * 获取当前PHP的运行模式
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return string
	 */
	public static function getMode() : string
	{
		return !is_string(php_sapi_name()) ? 'error' : php_sapi_name();
	}

	/**
	 * 判断当前的运行模式是否为CLI
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return boolean
	 */
	public static function isRunningWithCLI() : bool
	{
		return strpos(self::getMode(), 'cli') !== false;
	}

	/**
	 * 判断当前的运行模式是否为CGI
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return boolean
	 */
	public static function isRunningWithCGI() : bool
	{
		return strpos(self::getMode(), 'cgi') !== false;
	}

	/**
	 * 返回当前对象更好的类名
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  object      $class 实例化对象
	 * @return string
	 */
	public static function getShortClassName(object $class) : string
	{
		return basename(str_replace('\\', '/', get_class($class)));
	}

	/**
	 * 转义字符串中的斜杠
	 *
	 * @author HanskiJay
	 * @since  2021-05-29
	 * @param  string      &$str 所需字符串
	 * @return string
	 */
	public static function escapeSlash(string &$str) : string
	{
		return $str = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $str);
	}

	/**
	 * 返回HTML标签与换行
	 *
	 * @author HanskiJay
	 * @since  2022-08-03
	 * @param  string $searchString
	 * @param  string $globalString
	 * @return string
	 */
	public static function findTagNewline(string $searchString, string $globalString) : string
	{
		$tag = str_replace(['.', '/', '|', '$'], ['\.', '\/', '\|', '\$'], $searchString);
		if(preg_match("/(\s*?){1}{$tag}/i", $globalString, $m)) {
			return $m[0];
		}
		return $searchString;
	}
	/**
	 * 当前内存使用情况
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  integer $to 到小数点后面几位
	 * @return float
	 */
	public static function getCurrentMemoryUsage(bool $format = true, int $to = 2) : float
	{
		$memory = memory_get_usage();
		return $format ? round($memory / 1024 / 1024, $to) : $memory;
	}
}