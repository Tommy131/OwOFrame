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
	* 本类Session数据的存储格式采用json数据格式进行封装, 并且使用base64进行编码;
	* !注意: 使用此方法需要自行使用session_start();
************************************************************************/

namespace backend\system\utils;

class Session
{	
	public static $rewrite = false;

	// Func: 检查是否存在单个Session数据;
	public static function has(string $storeKey) : bool
	{
		return isset($_SESSION[$storeKey]);
	}
	// Func: 新增一个Session数据;
	public static function set(string $storeKey, $data) : void
	{
		if(!self::has($storeKey) || self::$rewrite) {
			$_SESSION[$storeKey] = $data;
		}
	}
	// Func: 获取一个Session数据;
	public static function get(string $storeKey, $default = null)
	{
		return $_SESSION[$storeKey] ?? $default;
	}
	// Func: 获取全部的Session数据;
	public static function getAll() : ?array
	{
		return $_SESSION ?? null;
	}
	// Func: 删除单个Session数据;
	public static function delete(string $storeKey) : void
	{
		if(self::has($storeKey)) {
			unset($_SESSION[$storeKey]);
		}
	}
	public static function start() : void
	{
		@session_start();
	}
	// Func: 重置当前客户端的Session数据;
	public static function reset() : void
	{
		$_SESSION = [];
	}
}
?>