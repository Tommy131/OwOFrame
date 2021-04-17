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
	* GitHub: https://github.com/Tommy131
	*
	* 杂项方法公共存放类

************************************************************************/

declare(strict_types=1);
namespace owoframe\helper;

use FilesystemIterator as FI;
use owoframe\contract\HTTPStatusCodeConstant;
use owoframe\contract\MIMETypeConstant;
use owoframe\utils\LogWriter;

class Helper implements HTTPStatusCodeConstant, MIMETypeConstant
{
	/* @string Android系统标识 */
	public const OS_ANDROID = 'android';
	/* @string Linux系统标识 */
	public const OS_LINUX   = 'linux';
	/* @string Windows系统标识 */
	public const OS_WINDOWS = 'windows';
	/* @string Mac系统标识 */
	public const OS_MACOS   = 'mac';
	/* @string BSD系统标识 */
	public const OS_BSD     = 'bsd';
	/* @string 未识别的系统标识 */
	public const OS_UNKNOWN = 'unknown';


	/**
	 * @method      isMobile
	 * @description 检测是否为移动设备访问
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      boolean|string
	 */
	public static function isMobile()
	{
		//获取USER AGENT
		$agent = strtolower(server('HTTP_USER_AGENT'));
		
		if(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $agent)) return true;
		elseif(strpos($agent, 'windows nt')) return false;
		else return $agent;
	}

	/**
	 * @method      getClientBrowser
	 * @description 获取客户端信息
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      string
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
	 * @method      getClientIp
	 * @description 获取客户端IP
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      string
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
	 * @method      isIp
	 * @description 判断传入的字符串是否为有效IP地址
	 * @author      HanskiJay
	 * @doneIn      2020-10-24
	 * @param       string      $ip 字符串
	 * @return      bool
	 */
	public static function isIp(string $ip) : bool
	{
		return (bool) preg_match("/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/", $ip);
	}

	/**
	 * @method      isDomain
	 * @description 判断字符串是否为域名
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string      $str 字符串
	 * @param       &$match
	 * @return      boolean
	 */
	public static function isDomain(string $str, &$match = null) : bool
	{
		if(strtolower($str) === 'localhost') return true;
		return (strpos($str, '--') === false) && preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?\.)?[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?[\.]([a-z]+)$/i', $str, $match);
	}

	/**
	 * @method      checkStrEncoding
	 * @description 检查是否是一个安全的主机名
	 * @param       string      $host 主机名
	 * @return      boolean
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
	 * @method      getOS
	 * @description 返回当前系统类型
	 * @author      HanskiJay
	 * @doenIn      2021-02-18
	 * @return      string
	 */
	public static function getOS() : string
	{
		$r  = null;
		$os = php_uname('s');
		if(stripos($os, 'linux') !== false) {
			$r = @file_exists('/system/build.prop') ? self::OS_ANDROID : self::LINUX;
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
	 * @method      mimeContentType
	 * @description 获取文件类型
	 * @param       string      $fileName 文件名
	 * @return      string
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
		if($size > 1)
		{
			$ext = $part[$size - 1];
			if(isset(self::MIMETYPE[$ext])) return self::MIMETYPE[$ext];
		}
		return 'application/octet-stream';
	}

	/**
	 * @method      getMimeType
	 * @description 获取所有的Mime类型
	 * @author      HanskiJay
	 * @return      array
	 */
	public static function getMimeType() : array
	{
		return self::MIMETYPE;
	}

	/**
	 * @method      logger
	 * @description 日志记录
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @param       string      $msg    信息
	 * @param       string      $prefix 称号
	 * @param       string      $level  等级
	 * @return      void
	 */
	public static function logger(string $msg, string $prefix = 'OwOCLI', string $level = 'INFO') : void
	{
		LogWriter::setFileName(self::isRunningWithCLI() ? 'owoblog_cli_run.log' : 'owoblog_run.log');
		LogWriter::write($msg, $prefix, $level);
	}

	/**
	 * @method      removeDir
	 * @description 删除文件夹
	 * @author      HanskiJay
	 * @doenIn      2021-04-17
	 * @param       string      $path 文件夹路径
	 * @return      void
	 */
	public static function removeDir(string $path) : void
	{
		if(!is_dir($path)) {
			return;
		}
		$files = iterator_to_array(new FI($path, FI::CURRENT_AS_PATHNAME | FI::SKIP_DOTS), false);

		foreach($files as $file) {
			if(is_file($file)) {
				unlink($file);
			}
			elseif(is_dir($file)) {
				self::removeDir($file);
				rmdir($file);
			}
		}
	}

	/**
	 * @method      getMode
	 * @description 获取当前PHP的运行模式
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      string
	 */
	public static function getMode() : string
	{
		return !is_string(php_sapi_name()) ? 'error' : php_sapi_name();
	}

	/**
	 * @method      isRunningWithCLI
	 * @description 判断当前的运行模式是否为CLI
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      boolean
	 */
	public static function isRunningWithCLI() : bool
	{
		return (bool) preg_match('/cli/i', self::getMode());
	}

	/**
	 * @method      isRunningWithCGI
	 * @description 判断当前的运行模式是否为CGI
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      boolean
	 */
	public static function isRunningWithCGI() : bool
	{
		return (bool) preg_match('/cgi/i', self::getMode());
	}

	/**
	 * @method      getShortClassName
	 * @description 返回当前对象更好的类名
	 * @author      HanskiJay
	 * @param       object      $class 实例化对象
	 * @return      string
	 */
	public static function getShortClassName(object $class) : string
	{
		return basename(str_replace('\\', '/', get_class($class)));
	}
}