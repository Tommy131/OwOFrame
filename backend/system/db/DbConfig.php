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
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

declare(strict_types=1);
namespace backend\system\db;

use backend\system\app\ModelBase;
use backend\system\exception\OwOFrameException;
use think\facade\Db;

class DbConfig extends Db
{
	/* @array ThinkPHP-ORM 数据库配置文件 */
	private static $dbConfig = [];

	public static function init() : void
	{
		self::$dbConfig = 
		[
			'default' => _global('mysql@default', 'mysql'),
			'connections' =>
			[
				_global('mysql@default', 'mysql') => 
				[
					// 数据库类型
					'type'     => _global('mysql@type', 'mysql'),
					// 主机地址
					'hostname' => _global('mysql@hostname', '127.0.0.1'),
					// 用户名
					'username' => _global('mysql@username', 'root'),
					// 密码
					'password' => _global('mysql@password', '123456'),
					// 数据库名
					'database' => _global('mysql@database', 'owocloud'),
					// 数据库编码默认采用utf8mb4
					'charset'  => _global('mysql@charset', 'utf8mb4'),
					// 数据库表前缀
					'prefix'   => _global('mysql@prefix', 'owo_'),
					// 数据库调试模式
					'debug'    => true
				]
			]
		];
		self::setConfig(self::$dbConfig);
	}

	/**
	 * @method      setDefault
	 * @description 设置默认数据库连接配置;
	 * @param       string[default|配置文件标识]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function setDefault(string $default) : void
	{
		if(self::hasDbConfig($default)) {
			self::$dbConfig['default'] = $default;
		}
		throw new OwOFrameException("Database configuration '{$default}' doesn't exists!");
	}

	/**
	 * @method      getDefault
	 * @description 获取默认的配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-09
	 * @param       string[index|键名]
	 * @param       mixed[default|默认返回值]
	 * @return      mixed
	 */
	public static function getDefault(string $index, $default ='')
	{
		return self::$dbConfig['connections'][self::$dbConfig['default']][$index] ?? $default;
	}

	/**
	 * @method      hasDbConfig
	 * @description 判断是否存在某一个数据库配置文件;
	 * @description Check if exists a database configutration;
	 * @param       string[nickName|配置文件标识]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function hasDbConfig(string $nickName) : bool
	{
		return isset(self::$dbConfig['connections'][$nickName]);
	}

	/**
	 * @method      addConfig
	 * @description 组合数据库配置文件;
	 * @description Merge the database configurations;
	 * @param       string[nickName|配置文件标识]
	 * @param       array[dbConfig|传入的数据]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function addConfig(string $nickName, array $dbConfig) : void
	{
		self::$dbConfig['connections'][$nickName] = $dbConfig;
	}

}