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

use owoframe\http\HttpManager as Http;
use owoframe\http\route\Router;
use owoframe\exception\InvalidAppException;
use owoframe\exception\ResourceMissedException;

class AppManager implements \owoframe\contract\Manager
{
	/* @string AppBase basic namespace */
	private static $basicAppClass = "owoframe\\application\\AppBase";
	/* @string Application路径 */
	private static $appPath = "";

	/**
	 * @method      setPath
	 * @description 设置App目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $path 目录
	 * @return      void
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
	 * @method      getPath
	 * @description 获取App目录
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	*/
	public static function getPath() : string
	{
		if(is_dir(self::$appPath)) {
			return self::$appPath;
		} else {
			throw new ResourceMissedException("Path", $path);
		}
	}

	/**
	 * @method      hasApp
	 * @description 判断是否存在一个Application
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @param       string       $appName app名称
	 * @param       &$class      &$class  向上传递存在的应用对象
	 * @return      boolean
	 */
	public static function hasApp(string $appName, &$class = null) : bool
	{
		$appName   = strtolower($appName);
		$file      = self::$appPath . $appName . DIRECTORY_SEPARATOR;
		$file      = $file . ucfirst($appName) . 'App.php';
		$namespace = null;
		$class     = null;
		if(is_file($file)) {
			$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach($content as $line) {
				if(preg_match('/^namespace\s(.*);$/i', $line, $match)) {
					$namespace = trim($match[1]);
				}
				elseif(preg_match('/^class\s(.*)$/i', $line, $match)) {
					$class = @array_shift(explode(" ", trim($match[1])));
					break;
				}
			}

			include_once($file);
			$class = "\\{$namespace}\\{$class}";
			if(!class_exists($class)) {
				throw new ResourceMissedException("Class", $class);
			}
			if((new \ReflectionClass($class))->getParentClass()->getName() !== self::$basicAppClass) {
				throw new InvalidAppException($appName, "Parent class should be interfaced by ".self::$basicAppClass);
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @method      getDefaultApp
	 * @description 获取默认端App
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      null or AppBase
	*/
	public static function getDefaultApp() : ?AppBase
	{
		return self::getApp(DEFAULT_APP_NAME);
	}

	/**
	 * @method      getApp
	 * @description 获取指定App
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $appName App名称
	 * @return      null|@AppBase
	*/
	public static function getApp(string $appName) : ?AppBase
	{
		if(self::hasApp($appName, $class)) {
			return new $class(Http::getCompleteUrl(), Router::getParameters());
		} else {
			return null;
		}
	}
}