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

use Composer\Autoload\ClassLoader;
use owoframe\constant\Manager;
use owoframe\module\ModuleLoader;
use owoframe\object\INI;
use owoframe\exception\ExceptionOutput;

use owoframe\application\AppManager;
use owoframe\console\Console;
use owoframe\event\EventManager;
use owoframe\http\FileUploader;
use owoframe\http\HttpManager as Http;
use owoframe\redis\RedisManager as Redis;


final class MasterManager implements Manager
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

	/**
	 * 绑定标签到类
	 *
	 * @access protected
	 * @var array
	 */
	protected $bind =
	[
		'app'          => AppManager::class,
		'console'      => Console::class,
		'event'        => EventManager::class,
		'fileuploader' => FileUploader::class,
		'http'         => Http::class,
		'redis'        => Redis::class,
		'unknown'      => null
	];

	/**
	 * 对象实例列表
	 *
	 * @access protected
	 * @var array
	 */
	protected $instances = [];



	public function __construct(?ClassLoader $classLoader = null)
	{
		if(version_compare(PHP_VERSION, '7.1.0') === -1) {
			die('[PHP_VERSION_TO_LOW] OwOWebFrame need to run at higher PHP version, minimum PHP 7.1.0.');
		}

		if(!self::isRunning()) {
			static::$instance = $this;
			if($classLoader !== null) {
				static::$classLoader = $classLoader;
			}
			self::initializeSystem();
			Container::getInstance()->bind('unknown', new class implements Manager {});
			if(INI::_global('system.autoInitDatabase', true) == true) {
				\owoframe\database\DbConfig::init();
			}
			AppManager::setPath(APP_PATH);
			ModuleLoader::setPath(MODULE_PATH);
			ModuleLoader::autoLoad($this);
			define('OWO_INITIALIZED', true); // Define this constant to let the system know that OwOFrame has been initialized;
		}
	}


	public function stop() : void
	{
		// TODO: 结束任务相关;
	}


	/**
	 * 返回选择的管理器
	 *
	 * @author HanskiJay
	 * @since  2021-03-04
	 * @param  string      $bindTag 绑定标识
	 * @param  array       $params  传入参数
	 * @return AppManager|Console|EventManager|FileUploader|Http|Redis|UserManager
	 */
	public function getManager(string $bindTag, array $params = []) : Manager
	{
		$bindTag = strtolower($bindTag);
		if(!isset($this->bind[$bindTag])) {
			$bindTag = 'unknown';
		}
		if(!isset($this->instances[$bindTag])) {
			$container = Container::getInstance();
			$container->bind($bindTag, $this->bind[$bindTag]);
			$this->instances[$bindTag] = $container->make($bindTag, $params);
		}
		return $this->instances[$bindTag];
	}

	/**
	 * 初始化系統需要
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 * @return void
	 */
	public static function initializeSystem() : void
	{
		if(!self::isRunning()) {
			// Set up exception crawling;
			set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
			set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);
			// Define OwOFrame start time;
			if(!defined('START_MICROTIME'))  define('START_MICROTIME', microtime(true));
			// Define the GitHub Page;
			if(!defined('GITHUB_PAGE'))      define('GITHUB_PAGE',     'https://github.com/Tommy131/OwOFrame/');
			// Define OwOFrame start time;
			if(!defined('APP_VERSION'))      define('APP_VERSION',     'dev@v1.0.1');
			// Check whether the current environment supports mbstring extension;
			if(!defined('MB_SUPPORTED'))     define('MB_SUPPORTED',    extension_loaded('mbstring'));
			// Project root directory (absolute path);
			if(!defined('ROOT_PATH'))        define('ROOT_PATH',       dirname(realpath(dirname(__FILE__)), 2) . DIRECTORY_SEPARATOR);
			// Project source directory (absolute path);
			if(!defined('OWO_PATH'))         define('OWO_PATH',        realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
			// Define Application path(absolute path);
			if(!defined('APP_PATH'))         define('APP_PATH',        ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);
			// Define Module path(absolute path);
			if(!defined('MODULE_PATH'))      define('MODULE_PATH',     ROOT_PATH . 'module' . DIRECTORY_SEPARATOR);
			// Define Storage path(absolute path);
			if(!defined('STORAGE_PATH'))     define('STORAGE_PATH',    ROOT_PATH . 'storages' . DIRECTORY_SEPARATOR);
			// Define Framework path(absolute path);
			if(!defined('FRAMEWORK_PATH'))   define('FRAMEWORK_PATH',  STORAGE_PATH . 'system' . DIRECTORY_SEPARATOR);
			// Cache files directory for Framework(absolute path);
			if(!defined('F_CACHE_PATH'))     define('F_CACHE_PATH',    FRAMEWORK_PATH . 'cache' . DIRECTORY_SEPARATOR);
			// Configuration files directory for Framework(absolute path);
			if(!defined('CONFIG_PATH'))     define('CONFIG_PATH',      FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR);
			// Cache files directory for Application(absolute path);
			if(!defined('A_CACHE_PATH'))     define('A_CACHE_PATH',    STORAGE_PATH . 'application' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
			// Log files directory (absolute path);
			if(!defined('LOG_PATH'))         define('LOG_PATH',        FRAMEWORK_PATH . 'logs' . DIRECTORY_SEPARATOR);
			// Define Resource path for Front-End(absolute path);
			if(!defined('RESOURCE_PATH'))    define('RESOURCE_PATH',   STORAGE_PATH . 'public' . DIRECTORY_SEPARATOR);
			// Define Public path for Front-End(absolute path);
			if(!defined('PUBLIC_PATH'))      define('PUBLIC_PATH',     ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);

			if(!is_dir(STORAGE_PATH))  mkdir(STORAGE_PATH,  755, true);
			if(!is_dir(F_CACHE_PATH))  mkdir(F_CACHE_PATH,  755, true);
			if(!is_dir(CONFIG_PATH))   mkdir(CONFIG_PATH,   755, true);
			if(!is_dir(LOG_PATH))      mkdir(LOG_PATH,      755, true);
			if(!is_dir(A_CACHE_PATH))  mkdir(A_CACHE_PATH,  755, true);
			if(!is_dir(RESOURCE_PATH)) mkdir(RESOURCE_PATH, 755, true);
			MasterManager::getClassLoader()->addPsr4('application' . DIRECTORY_SEPARATOR, APP_PATH);
			MasterManager::getClassLoader()->addPsr4('module' . DIRECTORY_SEPARATOR,      MODULE_PATH);
		}
		INI::globalLoad(owoConfigFile('global', 'ini'));
		// Define Timezone;
		if(!defined('TIME_ZONE')) define('TIME_ZONE', (INI::_global('owo.timeZone', 'Europe/Berlin')));
		date_default_timezone_set(TIME_ZONE);
		// Define default Application;
		if(!defined('DEFAULT_APP_NAME')) define('DEFAULT_APP_NAME', (INI::_global('owo.defaultApplication', 'index')));

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
		return !self::isRunning() ? -9.9999999 : round(microtime(true) - START_MICROTIME, 7);
	}

	/**
	 * 返回布尔值: 系统是否正在运行(已初始化)
	 *
	 * @author HanskiJay
	 * @since  2021-03-06
	 * @return boolean
	 */
	public static function isRunning() : bool
	{
		return defined('OWO_INITIALIZED');
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
	 * @return MasterManager
	 */
	public static function getInstance() : MasterManager
	{
		if(!static::$instance instanceof MasterManager) {
			static::$instance = new static;
		}
		return static::$instance;
	}
}