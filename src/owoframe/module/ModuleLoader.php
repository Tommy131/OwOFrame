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
namespace owoframe\module;

use owoframe\helper\Helper;
use owoframe\object\INI;
use owoframe\utils\LogWriter;
use owoframe\exception\ResourceMissedException;

class ModuleLoader
{
	/* @string 模块信息识别文件名称 */
	public const IDENTIFY_FILE_NAME = 'info.ini';

	/* @string 模块加载路径 */
	private static $loadPath = '';
	/* @array 模块池 */
	private static $modulePool = [];

	/**
	 * @method      setPath
	 * @description 设置模块加载路径
	 * @param       string[path|路径]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function setPath(string $path) : void
	{
		if(is_dir($path)) {
			self::$loadPath = $path;
		} else {
			throw new ResourceMissedException("Path", $path);
		}
	}

	/**
	 * @method      getPath
	 * @description 获取模块加载路径
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getPath() : string
	{
		return self::$loadPath;
	}


	/**
	 * @method      autoLoad
	 * @description 自动从加载路径加载模块
	 * @author      HanskiJay
	 * @doneIn      2021-01-23
	 * @return      void
	 */
	public static function autoLoad() : void
	{
		// try {
			$dirArray = scandir(self::getPath());
			// unset dots and pathname;
			unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);
			$path = [];
			foreach($dirArray as $name) {
				if(is_dir($dir = self::getPath() . $name . DIRECTORY_SEPARATOR) && is_file($dir . self::IDENTIFY_FILE_NAME)) {
					$path[$name] = $dir;
				}
			}
			// TODO: 读取配置文件后加载模块(Method::existsModule);
			foreach($path as $name => $dir) {
				if(!self::loadModule($dir, $name)) {
					LogWriter::write("Load module '{$name}' failed!", 'ModuleLoader', 'WARNING');
				}
			}
		// } catch(\Throwable $e) {

		// }
	}

	/**
	 * @method      existsModule
	 * @description 判断模块是否存在
	 * @author      HanskiJay
	 * @doneIn      2021-01-23
	 * @return      boolean
	 */
	public static function existsModule(string $name, &$info = null) : bool
	{
		if(isset(self::$modulePool[$name])) return true;
		// Start judgment;
		$hisPath = self::getPath() . $name . DIRECTORY_SEPARATOR;
		if(!is_dir($hisPath)) return false;
		if(!file_exists($ic = $hisPath . self::IDENTIFY_FILE_NAME)) return false;
		$info = new INI($ic);
		if(!self::checkInfo($info->getAll())) return false;
		$info = $info->obj(); // Format to JSON Object;
		if(!file_exists($hisPath .$info->className . '.php')) return false;
		/*$info->className = str_replace('/', '\\', trim($info->className));
		if(!class_exists($info->className)) return false;
		if(is_bool($c = (new \ReflectionClass($info->className))->getParentClass())) return false;
		if($c->getName() !== ModuleBase::className) return false;*/
		if(isset($info->onlyCLI) && $info->onlyCLI && !Helper::isRunningWithCLI()) return false;
		// End judgment;
		return true;
	}

	/**
	 * @method      getModule
	 * @description 获取模块实例化对象
	 * @author      HanskiJay
	 * @doneIn      2021-02-08
	 * @param       string[name|模块名称]
	 * @return      null or ModuleBase
	 */
	public static function getModule(string $name) : ?ModuleBase
	{
		return self::$modulePool[$name] ?? null;
	}

	/**
	 * @method      loadModule
	 * @description 加载模块
	 * @author      HanskiJay
	 * @doneIn      2021-01-23
	 * @param       string[dir|模块所在的路径]
	 * @param       string[name|模块名称]
	 * @return      boolean
	 */
	public static function loadModule(string $dir, string $name) : bool
	{
		if(self::existsModule($name, $info)) {
			// include_once($dir . $info->className . '.php');
			$namespace = $info->namespace ?? '';
			$class     = $namespace . '\\' . $info->className;

			if(class_exists($class)) {
				$class = self::$modulePool[$class] = new $class($dir, $info);
				$class->onLoad();
				$class->setEnabled();
			}
			return true;
		}
		return false;
	}

	public static function disableModule(string $name) : bool
	{
		if(($module = self::getModule($name)) !== null) {
			self::$modulePool[$name]->onDisable();
			self::$modulePool[$name]->setDisabled();
			unset(self::$modulePool[$name]);
		}
		return false;
	}

	/**
	 * @method      checkInfo
	 * @description 检查模块信息文件是否有效
	 * @author      HanskiJay
	 * @doneIn      2021-01-23
	 * @param       array[info|已加载的配置文件]
	 * @return      boolean
	 */
	public static function checkInfo(array $info, string &$missParam = '') : bool
	{
		return checkArrayValid($info, ['author', 'className', 'name', 'description', 'version', 'priority'], $missParam);
	}
}