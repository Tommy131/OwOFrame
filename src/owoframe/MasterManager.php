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
namespace owoframe;

use ReflectionClass;
use Composer\Autoload\ClassLoader;

use owoframe\interfaces\Unit;
use owoframe\object\INI;
use owoframe\exception\ExceptionOutput;

// Registered Managers;
use owoframe\application\AppManager;
use owoframe\event\EventManager;
use owoframe\http\HttpManager;

// Registered Widgets;
use owoframe\console\Console;
use owoframe\utils\Logger;
use owoframe\module\ModuleLoader;

final class MasterManager
{
	/**
	 * 主进程实例
	 *
	 * @access protected
	 * @var MasterManager
	 */
	private static $instance = null;

	/**
	 * ClassLoader实例
	 *
	 * @access private
	 * @var ClassLoader
	 */
	private static $classLoader;

	private $lists = [
		'app'     => AppManager::class,
		'console' => Console::class,
		'event'   => EventManager::class,
		'http'    => HttpManager::class,
		'logger'  => Logger::class,
	];
	/**
	 * 实例存储池
	 *
	 * @var array
	 */
	private $instancedPool = [];


	/**
	 * 构造函数
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 * @param  ClassLoader|null $classLoader
	 */
	public function __construct(?ClassLoader $classLoader = null)
	{
		if(!static::$instance instanceof MasterManager) {
			static::$instance = $this;
		}
		if($classLoader !== null) {
			static::$classLoader = $classLoader;
			$classLoader->addPsr4('application\\', APP_PATH);
			$classLoader->addPsr4('modules\\',     MODULE_PATH);
		}

		// Initialize storages directory folder;
		self::createStorageDirectory();

		// Generate global configuration file;
		self::generateConfig();

		// Set up exception crawling;
		set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
		set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);

		// Define Timezone;
		define('TIME_ZONE', (INI::_global('owo.timeZone', 'Europe/Berlin')));
		date_default_timezone_set(TIME_ZONE);

		if(INI::_global('system.autoInitDatabase', true) == true) {
			\owoframe\database\DbConfig::init();
		}
		ModuleLoader::autoLoad($this);
		// Auto create instance for Logger;
		$this->getUnit('logger');
	}

	/**
	 * 返回选择的管理器
	 *
	 * @author HanskiJay
	 * @since  2021-03-04
	 * @param  string      $bindTag 绑定标识
	 * @param  array       $params  传入参数
	 * @return Unit|AppManager|Console|EventManager|HttpManager|Logger
	 */
	public function getUnit(string $name) : ?Unit
	{
		// If the manager has been instantiated;
		if(isset($this->instancedPool[$name])) {
			return $this->instancedPool[$name];
		}

		if(isset($this->lists[$name])) {
			$reflect = new ReflectionClass($this->lists[$name]);
			if($reflect->implementsInterface(Unit::class)) {
				return $this->instancedPool[$name] = new $this->lists[$name]();
			}
		}
		return null;
	}

	/**
	 * 创建存储目录文件夹
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 */
	public static function createStorageDirectory() : void
	{
		if(!is_dir(F_CACHE_PATH))  mkdir(F_CACHE_PATH,  755, true);
		if(!is_dir(CONFIG_PATH))   mkdir(CONFIG_PATH,   755, true);
		if(!is_dir(LOG_PATH))      mkdir(LOG_PATH,      755, true);
		if(!is_dir(A_CACHE_PATH))  mkdir(A_CACHE_PATH,  755, true);
		if(!is_dir(RESOURCE_PATH)) mkdir(RESOURCE_PATH, 755, true);
	}

	/**
	 * 创建存储目录文件夹
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 */
	public static function generateConfig() : void
	{
		$ini = new INI(config_path('global.ini'), [
			'owo' => [
				'debugMode'  => true,
				'enableLog'  => false,
				'timeZone'   => 'Europe/Berlin',
				'defaultApp' => 'index',
				# 若存在多个禁止访问的Application, 请使用逗号分隔 (不能含有空格)
				# If you need deny more than 1 Application, please use comma to split (do not use space)
				# e.g.|例子: index,test,config
				'denyList'   => null
			],
			'mysql' => [
				'default'  => 'mysql',
				'type'     => 'mysql',
				'username' => 'root',
				'password' => '123456',
				'hostname' => '127.0.0.1',
				'port'     => 3306,
				'charset'  => 'utf8mb4',
				'database' => 'owoblogserver',
				'prefix'   => null
			],
			'redis' => [
				'enable' => true,
				'server' => '127.0.0.1',
				'port'   => 5300,
				'auth'   => '123456'
			],
			'system' => [
				'autoInitDatabase' => true
			],
			'view' => [
				'loopLevel'      => 3,
				'judgementLevel' => 3
			]
		], true);
		INI::loadObject2Global($ini);
	}

	/**
	 * 返回系统初始化到调用此函数的总共运行时间
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 * @return float
	 */
	public static function getRunTime() : float
	{
		return round(microtime(true) - START_MICROTIME, 7);
	}

	/**
	 * 返回类加载器
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 * @return ClassLoader|null
	 */
	public static function getClassLoader() : ?ClassLoader
	{
		return static::$classLoader;
	}

	/**
	 * 返回容器单例实例
	 *
	 * @author HanskiJay
	 * @since  2021-03-05
	 * @param  ClassLoader|null $classLoader
	 * @return MasterManager
	 */
	public static function getInstance(?ClassLoader $classLoader = null) : MasterManager
	{
		if(!static::$instance instanceof MasterManager) {
			static::$instance = new static($classLoader);
		}
		return static::$instance;
	}

	/**
	 * 设置类加载器
	 *
	 * @author HanskiJay
	 * @since  2022-05-27
	 * @param  ClassLoader $classLoader
	 * @param  boolean     $forceUpdate
	 * @return void
	 */
	public static function setClassLoader(ClassLoader $classLoader, bool $forceUpdate = false) : void
	{
		if(($classLoader === null) || $forceUpdate) {
			static::$classLoader = $classLoader;
		}
	}

	/**
	 * 允许通过静态调用 `getUnit` 方法
	 *
	 * @author HanskiJay
	 * @since  2022-05-27
	 * @param  string $name 绑定标识
	 * @return Unit|AppManager|Console|EventManager|HttpManager|Logger
	 */
	public static function _getUnit(string $name) : ?Unit
	{
		return self::getInstance()->getUnit($name);
	}
}