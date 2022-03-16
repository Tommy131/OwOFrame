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
namespace owoframe\application;

use FilesystemIterator as FI;
use owoframe\exception\InvalidAppException;
use owoframe\exception\ResourceMissedException;
use owoframe\http\HttpManager as Http;
use owoframe\http\route\Router;
use owoframe\utils\Logger;

class AppManager implements \owoframe\constant\Manager
{
	/**
	 * AppBase basic namespace
	 *
	 * @access private
	 * @var string
	 */
	private static $basicAppClass = "owoframe\\application\\AppBase";

	/**
	 * Application路径
	 *
	 * @access private
	 * @var string
	 */
	private static $appPath = "";



	/**
	 * 设置App目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  string      $path 目录
	 * @return void
	 */
	public static function setPath(string $path) : void
	{
		if(is_dir($path)) {
			self::$appPath = $path;
		} else {
			throw new ResourceMissedException("Path", $path);
		}
	}

	/**
	 * 获取App目录
	 *
	 * @return string
	 * @author HanskiJay
	 * @since  2020-09-09
	 */
	public static function getPath() : string
	{
		if(is_dir(self::$appPath)) {
			return self::$appPath;
		} else {
			throw new ResourceMissedException("Path", self::$appPath);
		}
	}

	/**
	 * 判断是否存在一个Application
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @param  string      $appName app名称
	 * @param  &           &$class  向上传递存在的应用对象
	 * @return boolean
	 */
	public static function hasApp(string $appName, &$class = null) : bool
	{
		$name    = strtolower($appName);
		$appName = ucfirst($name);
		$class   = "\\application\\{$name}\\{$appName}" . 'App';

		if(!class_exists($class)) {
			if(DEBUG_MODE) throw new ResourceMissedException("Class", $class);
			return false;
		}
		if((new \ReflectionClass($class))->getParentClass()->getName() !== self::$basicAppClass) {
			if(DEBUG_MODE) throw new InvalidAppException($appName, "Parent class should be interfaced by ".self::$basicAppClass);
		}
		return true;
	}

	/**
	 * 获取默认端App
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @return AppBase|null
	 */
	public static function getDefaultApp() : ?AppBase
	{
		return self::getApp(DEFAULT_APP_NAME);
	}

	/**
	 * 获取指定App
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  string      $appName App名称
	 * @return AppBase|null
	 */
	public static function getApp(string $appName) : ?AppBase
	{
		static $application;
		if(isset($application[$appName]) && $application[$appName] instanceof AppBase) {
			return $application[$appName];
		}

		if(self::hasApp($appName, $class)) {
			return $application[$appName] = new $class(Http::getCompleteUrl(), Router::getParameters());
		}
		return null;
	}


	public function initializeApplications() : void
	{
		$path = new FI(APP_PATH, FI::KEY_AS_PATHNAME | FI::KEY_AS_FILENAME | FI::SKIP_DOTS);
		foreach($path as $info) {
			if(is_dir($info->getPathName())) {
				$appName = $info->getFileName();
				// 过滤文件名;
				if(!preg_match('/[a-z0-9]+/i', $appName)) continue;
				self::getApp($appName);
			}
		}
	}
}