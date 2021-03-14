<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2021 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Tommy131)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	* GitHub: https://github.com/Tommy131

************************************************************************/

declare(strict_types=1);
namespace owoframe\helper;

use owoframe\MasterManager;
use owoframe\exception\ExceptionOutput;

class BootStraper
{

	/**
	 * @method      initializeSystem
	 * @description 初始化系統需要
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @return      void
	 */
	public static function initializeSystem() : void
	{
		if(version_compare(PHP_VERSION, '7.1.0') === -1) {
			die('[PHP_VERSION_TO_LOW] OwOWebFrame need to run at higher PHP version, minimum PHP 7.1.x.');
		}

		if(!self::isRunning()) {
			set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
			set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);
			// Define OwOFrame start time;
			if(!defined('START_MICROTIME'))  define('START_MICROTIME', microtime(true));
			// Define Timezone;
			if(!defined('TIME_ZONE'))        define('TIME_ZONE',       'Europe/Berlin');
			// Define OwOFrame start time;
			if(!defined('APP_VERSION'))      define('APP_VERSION',     '20210307_dev@v1.0.1');
			// Check whether the current environment supports mbstring extension;
			if(!defined('MB_SUPPORTED'))     define('MB_SUPPORTED',    function_exists('mb_get_info') && function_exists('mb_regex_encoding'));
			// Project root directory (absolute path);
			if(!defined('ROOT_PATH'))        define('ROOT_PATH',       dirname(realpath(dirname(__FILE__)), 3) . DIRECTORY_SEPARATOR);
			// Project source directory (absolute path);
			if(!defined('OWO_PATH'))         define('OWO_PATH',        dirname(realpath(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
			// Define Aplication path(absolute path);
			if(!defined('APP_PATH'))         define('APP_PATH',        ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);
			// Define Module path(absolute path);
			if(!defined('MODULE_PATH'))      define('MODULE_PATH',     ROOT_PATH . 'module' . DIRECTORY_SEPARATOR);
			// Define Storage path(absolute path);
			if(!defined('STORAGE_PATH'))     define('STORAGE_PATH',    ROOT_PATH . 'storages' . DIRECTORY_SEPARATOR);
			// Define Framework path(absolute path);
			if(!defined('FRAMEWORK_PATH'))   define('FRAMEWORK_PATH',  STORAGE_PATH . 'framework' . DIRECTORY_SEPARATOR);
			// Cache files directory for Back-End(absolute path);
			if(!defined('CACHE_PATH'))       define('CACHE_PATH',      FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
			// Log files directory (absolute path);
			if(!defined('LOG_PATH'))         define('LOG_PATH',        STORAGE_PATH . 'logs' . DIRECTORY_SEPARATOR);
			// Define Resource path for Front-End(absolute path);
			if(!defined('RESOURCE_PATH'))    define('RESOURCE_PATH',   ROOT_PATH . 'resources' . DIRECTORY_SEPARATOR);
			// Define Public path for Front-End(absolute path);
			if(!defined('PUBLIC_PATH'))      define('PUBLIC_PATH',     ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);

			if(!is_dir(STORAGE_PATH))  mkdir(STORAGE_PATH,  755, true);
			if(!is_dir(CACHE_PATH))    mkdir(CACHE_PATH,    755, true);
			if(!is_dir(LOG_PATH))      mkdir(LOG_PATH,      755, true);
			if(!is_dir(RESOURCE_PATH)) mkdir(RESOURCE_PATH, 755, true);
			date_default_timezone_set(TIME_ZONE);
			MasterManager::getClassLoader()->addPsr4('application\\', APP_PATH);
			MasterManager::getClassLoader()->addPsr4('module\\',      MODULE_PATH);
		}
		loadConfig(FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'global.ini');
	}

	/**
	 * @method      getRunTime
	 * @description 返回系统初始化到调用此函数的总共运行时间
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @return      float
	 */
	public static function getRunTime() : float
	{
		return !self::isRunning() ? -9.9999999 : round(microtime(true) - START_MICROTIME, 7);
	}

	/**
	 * @method      isRunning
	 * @description 返回布尔值: 系统是否正在运行(已初始化)
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @return      boolean
	 */
	public static function isRunning() : bool
	{
		return defined('OWO_INITIALIZED');
	}
}