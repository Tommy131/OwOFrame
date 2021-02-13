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

namespace backend\system\http;

class Session
{	
	/* @bool 重写数据 */
	public static $rewrite = false;


	/**
	 * @method      has
	 * @description 检查是否存在单个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function has(string $storeKey) : bool
	{
		return isset($_SESSION[$storeKey]);
	}

	/**
	 * @method      has
	 * @description 新增一个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function set(string $storeKey, $data) : void
	{
		if(!self::has($storeKey) || self::$rewrite) {
			$_SESSION[$storeKey] = $data;
		}
	}

	/**
	 * @method      has
	 * @description 获取一个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function get(string $storeKey, $default = null)
	{
		return $_SESSION[$storeKey] ?? $default;
	}

	/**
	 * @method      has
	 * @description 获取全部的Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function getAll() : ?array
	{
		return $_SESSION ?? null;
	}

	/**
	 * @method      has
	 * @description 删除单个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function delete(string $storeKey) : void
	{
		if(self::has($storeKey)) {
			unset($_SESSION[$storeKey]);
		}
	}

	/**
	 * @method      start
	 * @description 启动Session
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @return      void
	 */
	public static function start() : void
	{
		try {

			session_start();
		} catch(\Throwable $e) {
			throwError($e->getMessage(), __FILE__, __LINE__);
		}
	}

	/**
	 * @method      has
	 * @description 重置当前客户端的Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string[storeKey|存储名]
	 * @return      boolean
	 */
	public static function reset() : void
	{
		$_SESSION = [];
	}
}
?>