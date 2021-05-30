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
use owoframe\application\AppManager;
use owoframe\console\Console;
use owoframe\contract\Manager;
use owoframe\event\EventManager;
use owoframe\helper\BootStraper as BS;
use owoframe\helper\Helper;
use owoframe\http\FileUploader;
use owoframe\http\HttpManager as Http;
use owoframe\module\ModuleLoader;
use owoframe\redis\RedisManager as Redis;

final class MasterManager extends Container implements Manager
{
	/* @ClassLoader */
	private static $classLoader;
	/* @array 绑定标签到类 */
	protected $bind =
	[
		'console'      => Console::class,
		'event'        => EventManager::class,
		'fileuploader' => FileUploader::class,
		'http'         => Http::class,
		'redis'        => Redis::class,
		'unknown'      => null
	];



	public function __construct(?ClassLoader $classLoader = null)
	{
		if(!BS::isRunning()) {
			if($classLoader !== null) {
				self::$classLoader = $classLoader;
			}
			BS::initializeSystem();
			$this->bind('unknown', new class implements Manager {});

			foreach(['DEBUG_MODE', 'LOG_ERROR', 'DEFAULT_APP_NAME', 'DENY_APP_LIST'] as $define) {
				if(Helper::isRunningWithCLI()) {
					if(($define === 'DEFAULT_APP_NAME') || ($define === 'DENY_APP_LIST')) {
						continue;
					}
				}
				if(!defined($define)) {
					throw error("Constant parameter '{$define}' not found!");
				}
			}
			AppManager::setPath(APP_PATH);
			ModuleLoader::setPath(MODULE_PATH);
			ModuleLoader::autoLoad();
			define('OWO_INITIALIZED', true); // Define this constant to let the system know that OwOFrame has been initialized;
		}
	}


	public function stop() : void
	{
		// TODO: 结束任务相关;
	}

	/**
	 * @method      getManager
	 * @description 返回选择的管理器
	 * @author      HanskiJay
	 * @doenIn      2021-03-04
	 * @param       string      $bindTag 绑定标识
	 * @param       array       $params  传入参数
	 * @return      @Manager
	 */
	public function getManager(string $bindTag, array $params = []) : Manager
	{
		return $this->make($bindTag ?? 'unknown', $params);
	}

	/**
	 * @method      isRunning
	 * @description 返回系统运行状态
	 * @author      HanskiJay
	 * @doenIn      2021-03-04
	 * @return      boolean
	 */
	public static function isRunning() : bool
	{
		return BS::isRunning();
	}

	/**
	 * @method      getClassLoader
	 * @description 返回类加载器
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @return      null|@ClassLoader
	 */
	public static function getClassLoader() : ?ClassLoader
	{
		return self::$classLoader;
	}
}