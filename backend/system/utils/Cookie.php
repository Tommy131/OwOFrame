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

namespace backend\system\utils;

class Cookie
{
	private static $_prefix = '';
	private static $_path = '/';
	
	public static function setPrefix(string $url) : void
	{
		self::$_prefix = md5($url);
		$parsed = parse_url($url);

		/** 在路径后面强制加上斜杠 */
		self::$_path = $parsed['path'].'/';
	}
	
	public static function getPrefix() : string
	{
		return self::$_prefix;
	}
	
	public static function getPath() : string
	{
		return self::$_path;
	}
	
	public static function getAll() : ?array
	{
		return $_COOKIE ?? null;
	}
	
	public static function get(string $key, $default = NULL)
	{
		$key = self::$_prefix . $key;
		$value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : (isset($_POST[$key]) ? $_POST[$key] : $default);
		return is_array($value) ? $default : $value;
	}

	public static function has(string $key) : bool
	{
		return isset($_COOKIE[$key]);
	}
	
	public static function set(string $key, $value, $expire = 0) : void
	{
		$key = self::$_prefix . $key;
		setrawcookie($key, rawurlencode($value), $expire, self::$_path);
		$_COOKIE[$key] = $value;
	}
	
	public static function delete(string $key) : bool
	{
		$key = self::$_prefix . $key;
		if (!isset($_COOKIE[$key])) {
			return false;
		}
		
		setcookie($key, '', time() - 2592000, self::$_path);
		unset($_COOKIE[$key]);
		return true;
	}
}
?>