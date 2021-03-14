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

************************************************************************/

declare(strict_types=1);
namespace owoframe\http;

use owoframe\exception\OwOFrameException;
use owoframe\redis\RedisConnector;

class Session
{	
	/* @bool 重写数据 */
	public static $rewrite = false;


	/**
	 * @method      has
	 * @description 检查是否存在单个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string      $storeKey 存储名
	 * @return      boolean
	 */
	public static function has(string $storeKey) : bool
	{
		return isset($_SESSION[$storeKey]);
	}

	/**
	 * @method      set
	 * @description 新增一个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string      $storeKey 存储名
	 * @param       mixed       $data     数据
	 * @return      void
	 */
	public static function set(string $storeKey, $data) : void
	{
		if(!self::has($storeKey) || self::$rewrite) {
			$_SESSION[$storeKey] = $data;
		}
	}

	/**
	 * @method      get
	 * @description 获取一个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string      $storeKey 存储名
	 * @param       mixed       $default  默认返回结果
	 * @return      mixed
	 */
	public static function get(string $storeKey, $default = null)
	{
		return $_SESSION[$storeKey] ?? $default;
	}

	/**
	 * @method      getAll
	 * @description 获取全部的Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string      $storeKey 存储名
	 * @return      array
	 */
	public static function getAll() : array
	{
		return $_SESSION ?? [];
	}

	/**
	 * @method      delete
	 * @description 删除单个Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @param       string      $storeKey 存储名
	 * @return      void
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
			if(_global('redis@enable', true) && extension_loaded("redis"))
			{
				if(strtolower(ini_get("session.save_handler")) === "files") {
					ini_set("session.save_handler", "redis");
				}
				$server = _global('redis@server', '127.0.0.1');
				$port   = _global('redis@port', 6379);
				$auth   = _global('redis@auth', null);
				
				$connector = RedisConnector::getInstance();
				$connector->cfg('host',     $server, true);
				$connector->cfg('port',     $port,   true);
				$connector->cfg('password', $auth,   true);
				
				if($redis = $connector->getConnection()) {
					$connector->forceUsePassword();
				} else {
					throw new OwOFrameException('Could not use Redis for Session saver!');
				}

				$auth   = ($auth !== null) ? "?auth={$auth}" : '';
				ini_set("session.save_path", "tcp://{$server}:{$port}{$auth}");
			}
			session_start();
		} catch(\Throwable $e) {
			throw error($e->getMessage());
		}
	}

	/**
	 * @method      reset
	 * @description 重置当前客户端的Session数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-13
	 * @return      void
	 */
	public static function reset() : void
	{
		$_SESSION = [];
	}
}
?>