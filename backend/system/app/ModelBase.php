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

declare(strict_types=1);
namespace backend\system\app;

use backend\system\app\AppBase;
use backend\system\db\DbConfig;

abstract class ModelBase extends \think\Model
{
	/* @string 数据库配置信息(备份) */
	// protected static $dbConfig_bak = [];
	/* @ModelBase 返回自身实例 */
	protected static $instance = null;
	/* @string 数据库配置信息 */
	protected static $dbConfig;
	/* @string 数据库配置友好名称(用于ThinkPHP-ORM的数据库多连接) */
	protected static $dbNickName;


	public function __construct(array $data = [])
	{
		// NOTHING TO DO...
		// 这里仅进行构造函数的重写, 若需使用父级构造函数, 请使用下方方法.
	}

	public function initialize(array $data = [])
	{
		parent::__construct($data);
	}


	/**
	 * @method      config
	 * @description 返回ThinkPHP-ORM模型类
	 * @description Return object from ThinkPHP-ORM::Model
	 * @param       array[dbConfig|传入的数据]
	 * @param       bool[forceUpdate|强制更新配置](Default: false)
	 * @return      array
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function config(array $dbConfig, bool $forceUpdate = false) : array
	{
		if(empty(self::$dbConfig) || $forceUpdate) {
			self::$dbConfig = $dbConfig;
			// self::$dbConfig_bak = $dbConfig;
			// TODO: 在AppBase中添加对应的setConfig方法, 直接设置到ThinkPHP-ORM::Db->setConfig()
			DbConfig::addConfig(self::$dbNickName, $dbConfig);
		}
		return self::$dbConfig;
	}

	/**
	 * @method      getIndex
	 * @description 获取数据库配置中的某个元素
	 * @description Get a element from global value $dbConfig
	 * @param       string[index|配置索引]
	 * @param       string[default|默认返回值]
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function getIndex(string $index, string $default = '') : string
	{
		return self::$dbConfig[$index] ?? DbConfig::getDefault($index) ?? $default;
	}

	/**
	 * @method      getAll
	 * @description 获取数据库配置
	 * @description Get global value $dbConfig
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function getAll() : array
	{
		return self::$dbConfig;
	}

	/**
	 * @method      setIndex
	 * @description 设置数据库配置某项元素的值
	 * @description Set the value into a element from global value $dbConfig
	 * @param       string[index|配置索引]
	 * @param       string[value|更新值]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function setIndex(string $index, string $value) : void
	{
		// if(isset(DbConfig::DEFAULT_DB_CONFIG[$index])) {
			self::$dbConfig[$index] = $value;
		// }
	}

	/**
	 * @method      setName
	 * @description 设置数据库配置友好名称
	 * @description Set the name for database configuration
	 * @param       string[nickName|友好名称]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function setName(string $nickName) : void
	{
		if(empty(self::$dbNickName)) {
			self::$dbNickName = $nickName;
		}
	}

	/**
	 * @method      getName
	 * @description 获取数据库配置友好名称
	 * @description Get the name for database configuration
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public static function getNickName() : string
	{
		return self::$dbNickName;
	}

	/**
	 * @method      reset
	 * @description 还原数据库配置到最初的配置状态
	 * @description Set the value into a element from global value $dbConfig
	 * @param       string[index|配置索引]
	 * @param       string[value|更新值]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	/*public static function reset() : void
	{
		self::$dbConfig = self::$dbConfig_bak;
	}*/

	/**
	 * @method      getInstance
	 * @description 返回本类实例(作废)
	 * @description Return this class object
	 * @return      ModelBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-19 18:03
	*/
	public final static function getInstance() : ModelBase
	{
		if(!static::$instance instanceof ModelBase) {
			static::$instance = new static;
		}
		return static::$instance;
	}
}