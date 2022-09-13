<?php

/*********************************************************************
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

**********************************************************************/

declare(strict_types=1);
namespace owoframe\database;

use think\facade\Db;

use owoframe\exception\OwOFrameException;

class DbConfig extends Db
{
	/**
	 * ThinkPHP-ORM 数据库配置文件
	 *
	 * @access private
	 * @var array
	 */
	private static $dbConfig = [];



	/**
	 * 初始化数据库配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @return void
	 */
	public static function init() : void
	{
		static::$dbConfig =
		[
			'default' => _global('mysql.default', 'mysql'),
			'connections' =>
			[
				_global('mysql.default', 'mysql') =>
				[
					// 数据库类型
					'type'     => _global('mysql.type', 'mysql'),
					// 主机地址
					'hostname' => _global('mysql.hostname', '127.0.0.1'),
					// 用户名
					'username' => _global('mysql.username', 'root'),
					// 密码
					'password' => _global('mysql.password', '123456'),
					// 数据库名
					'database' => _global('mysql.database', 'owocloud'),
					// 数据库编码默认采用utf8mb4
					'charset'  => _global('mysql.charset', 'utf8mb4'),
					// 数据库表前缀
					'prefix'   => _global('mysql.prefix', 'owo_'),
					// 数据库调试模式
					'debug'    => _global('mysql.debugMode', true)
				]
			]
		];
		self::setConfig(static::$dbConfig);
		// 定义初始化标识;
		if(!defined('DB_INIT')) {
			define('DB_INIT', true);
		}
	}

	/**
	 * 设置默认数据库连接配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $tag 配置文件标识
	 * @return void
	 */
	public static function setDefaultConfig(string $tag) : void
	{
		if(self::hasDbConfig($tag)) {
			static::$dbConfig['default'] = $tag;
		}
		throw new OwOFrameException("Database configuration '{$tag}' doesn't exists!");
	}

	/**
	 * 从默认的配置文件获取配置
	 *
	 * @author HanskiJay
	 * @since  2021-01-09
	 * @param  string      $index   键名
	 * @param  mixed       $default 默认返回值
	 * @return mixed
	 */
	public static function getIndexFromDefault(string $index, $default = '')
	{
		return static::$dbConfig['connections'][static::$dbConfig['default']][$index] ?? $default;
	}

	/**
	 * 设置数据库配置某项元素的值
	 *
	 * @author HanskiJay
	 * @since  2020-09-19
	 * @param  string      $tag     配置文件标识
	 * @param  string      $index 配置索引
	 * @param  string      $value 更新值
	 * @return void
	 */
	public static function setIndex(string $tag, string $index, string $value) : void
	{
		static::$dbConfig['connections'][$tag][$index] = $value;
	}

	/**
	 * 获取数据库配置中的某个元素
	 *
	 * @author HanskiJay
	 * @since  2020-09-19 18:03
	 * @param  string      $tag     配置文件标识
	 * @param  string      $index   配置索引
	 * @param  string      $default 默认返回值
	 * @return string
	 */
	public static function getIndex(string $tag, string $index, string $default = '') : string
	{
		return static::$dbConfig['connections'][$tag][$index] ?? $default;
	}

	/**
	 * 获取所有数据库配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-19
	 * @return string
	 */
	public static function getAll() : array
	{
		return static::$dbConfig;
	}

	/**
	 * 判断是否存在某一个数据库配置文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $tag 配置文件标识
	 * @return boolean
	 */
	public static function hasDbConfig(string $tag) : bool
	{
		return isset(static::$dbConfig['connections'][$tag]);
	}

	/**
	 * 返回默认配置文件标识
	 *
	 * @author HanskiJay
	 * @since  2021-12-27
	 * @return string
	 */
	public static function getDefaultTag() : string
	{
		return static::$dbConfig['default'];
	}

	/**
	 * 组合数据库配置文件;
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $tag 配置文件标识
	 * @param  array       $dbConfig 传入的数据
	 * @return void
	 */
	public static function addConfig(string $tag, array $dbConfig) : void
	{
		static::$dbConfig['connections'][$tag] = $dbConfig;
	}

}