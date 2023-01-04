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

class ThinkDB extends \think\facade\Db
{
	/**
	 * 数据表名称
	 *
	 * @var string
	 */
	public static $TABLE_NAME = 'owo_test';

	/**
	 * 初始化数据库配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @return void
	 */
    public static function init() : void
	{
	    if(is_database_initialized() || !_global('system.autoInitDatabase', true)) {
			return;
		}

		self::setConfig([
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
		]);

		// 定义初始化标识;
		define('DB_INIT', true);
	}

	/**
	 * 通过指定字段和值返回所有匹配条件的数据
	 *
	 * @author HanskiJay
	 * @since  2021-12-25
	 * @param  string $where
	 * @param  mixed  $v
	 * @return array|null
	 */
    public static function get(string $where, $v) : ?array
	{
	    return self::db()->where($where, $v)->findOrEmpty();
	}

	/**
	 * 通过指定的字段和值返回一条字段数据
	 *
	 * @author HanskiJay
	 * @since  2021-12-25
	 * @param  string $where
	 * @param  mixed  $v
	 * @return mixed
	 */
    public static function getValue(string $where, $v, string $searched)
	{
	    return self::db()->where($where, $v)->value($searched);
	}

	/**
	 * 将以数组的形式返回当前的数据表数据转
	 *
	 * @author HanskiJay
	 * @since  2021-12-27
	 * @return array
	 */
    public static function getAll() : array
	{
	    return self::db()->select()->toArray();
	}

	/**
	 * 判断某一个字段是否存在值
	 *
	 * @author HanskiJay
	 * @since  2021-12-27
	 * @param  string  $where
	 * @param  mixed   $searched
	 * @return boolean
	 */
    public static function exists(string $where, $searched) : bool
	{
	    return !empty(self::get($where, $searched));
	}


	/**
	 * 判断当前数据表是否存在
	 *
	 * @author HanskiJay
	 * @since  2021-12-26
	 * @param  string $table_name
	 * @return boolean
	 */
    public static function isTableExists(?string $table_name = null) : bool
	{
		if(is_null($table_name)) {
			$table_name = static::$TABLE_NAME;
		}
	    return count(self::query('select table_name from information_schema.TABLES where table_name = \'' . $table_name . '\'')) > 0;
	}

	/**
	 * 使用限制搜索
	 *
	 * @author HanskiJay
	 * @since  2021-12-28
	 * @param  integer $count
	 * @return Query
	 */
    public static function selectWithLimit(int $count) : \think\db\Query
	{
	    return self::db()->limit($count);
	}

	/**
	 * 返回ThinkPHP-ORM种的\think\db\Query对象
	 *
	 * @author HanskiJay
	 * @since  2021-09-30
	 * @param  int|integer      $mode       选择表模式(0: 带前缀选择 1: 从配置系统文件的前缀选择)
	 * @param  string|null      $table_name 查询表名(默认从本类静态变量 $table_name 获取)
	 * @return  \think\db\Query
	 */
    public static function db(?string $table_name = null, int $mode = 0) : \think\db\Query
	{
		$method = ($mode === 0) ? 'table' : 'name';
	    return self::{$method}($table_name ?? static::$TABLE_NAME);
	}
}