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
namespace backend\system\plugin;

use backend\OwOFrame;
use backend\system\utils\LogWriter;
use backend\system\exception\ResourceMissedException;

class PluginLoader
{
	/* @string 插件信息识别文件名称 */
	public const IDENTIFY_FILE_NAME = 'info.conf';

	/* @string 插件加载路径 */
	private static $loadPath = '';
	/* @array 插件池 */
	private static $pluginPool = [];
	
	/**
	 * @method      setPath
	 * @description 设置插件加载路径
	 * @param       string[path|路径]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function setPath(string $path) : void
	{
		if(is_dir($path)) {
			self::$loadPath = $path;
			$classLoader = \OwOBootstrap\classLoader();
			$classLoader->addPath($path);
			$classLoader->register(true);
		} else {
			throw new ResourceMissedException("Path", $path);
		}
	}

	/**
	 * @method      getPath
	 * @description 获取插件加载路径
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
	 * @description 自动从加载路径加载插件
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @return      void
	 */
	public static function autoLoad() : void
	{
		try {
			$dirArray = scandir(self::getPath());
			// unset dots and pathname;
			unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);
			$path = [];
			foreach($dirArray as $name) {
				if(is_dir($dir = self::getPath() . $name . DIRECTORY_SEPARATOR) && is_file($dir . self::IDENTIFY_FILE_NAME)) {
					$path[$name] = $dir;
				}
			}
			// TODO: 读取配置文件后加载插件(Method::existsPlugin);
			foreach($path as $name => $dir) {
				if(!self::loadPlugin($dir, $name)) {
					LogWriter::write("Load plugin '{$name}' failed!", 'PluginLoader', 'WARNING');
				}
			}
		} catch(\Throwable $e) {

		}
	}

	/**
	 * @method      existsPlugin
	 * @description 判断插件是否存在
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @return      boolean
	 */
	public static function existsPlugin(string $name, &$info = []) : bool
	{
		if(isset(self::$pluginPool[$name])) return true;
		// Start judgment;
		$hisPath = self::getPath() . $name . DIRECTORY_SEPARATOR;
		if(!is_dir($hisPath)) return false;
		if(!file_exists($ic = $hisPath . self::IDENTIFY_FILE_NAME)) return false;
		$info = @array_shift(loadConfig($ic));
		if(!self::checkInfo($info)) return false;
		$info = json_decode(json_encode($info)); // Format to JSON Object;
		if(!file_exists($hisPath .$info->className . '.php')) return false;
		/*$info->className = str_replace('/', '\\', trim($info->className));
		if(!class_exists($info->className)) return false;
		if(is_bool($c = (new \ReflectionClass($info->className))->getParentClass())) return false;
		if($c->getName() !== PluginBase::className) return false;*/
		if(isset($info->onlyCLI) && $info->onlyCLI && !OwOFrame::isRunningWithCLI()) return false;
		// End judgment;
		return true;
	}

	/**
	 * @method      getPlugin
	 * @description 获取插件实例化对象
	 * @author      HanskiJay
	 * @doenIn      2021-02-08
	 * @param       string[name|插件名称]
	 * @return      null or PluginBase
	 */
	public static function getPlugin(string $name) : ?PluginBase
	{
		return self::$pluginPool[$name] ?? null;
	}

	/**
	 * @method      loadPlugin
	 * @description 加载插件
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @param       string[dir|插件所在的路径]
	 * @param       string[name|插件名称]
	 * @return      boolean
	 */
	public static function loadPlugin(string $dir, string $name) : bool
	{
		if(self::existsPlugin($name, $info)) {
			// include_once($dir . $info->className . '.php');
			$namespace = $info->namespace ?? '';
			$class     = $namespace . '\\' . $info->className;

			if(class_exists($class)) {
				$class = self::$pluginPool[$class] = new $class($dir, $info);
				$class->onLoad();
				$class->setEnabled();
			}
			return true;
		}
		return false;
	}

	public static function disablePlugin(string $name) : bool
	{
		if(($plugin = self::getPlugin($name)) !== null) {
			self::$pluginPool[$name]->onDisable();
			self::$pluginPool[$name]->setDisabled();
			unset(self::$pluginPool[$name]);
		}
		return false;
	}

	/**
	 * @method      checkInfo
	 * @description 检查插件信息文件是否有效
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @param       array[info|已加载的配置文件]
	 * @return      boolean
	 */
	public static function checkInfo(array $info, string &$missParam = '') : bool
	{
		return OwOFrame::checkArrayValid($info, ['author', 'className', 'name', 'description', 'version', 'priority'], $missParam);
	}
}