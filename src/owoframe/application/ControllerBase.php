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

use owoframe\helper\Helper;
use owoframe\http\route\Router;

abstract class ControllerBase
{
	/* @AppBase 返回AppBase实例 */
	private $app = null;
	/* @bool Front-End开启或关闭UsedTimeDiv(Default:true) */
	public static $showUsedTimeDiv = true;
	/* @string 若请求的Url中包含无效的请求方法, 则默认执行该方法 */
	public static $autoInvoke_methodNotFound = 'methodNotFound';


	public function __construct(AppBase $app)
	{
		$this->app = $app;
	}

	/**
	 * 这只是一个示例, 参考上方注释
	 *
	 * @author HanskiJay
	 * @since  2020-10-08 22:04
	 * @return mixed
	*/
	public function methodNotFound()
	{
		return 'Requested method \'' . Router::getCurrentRequestMethod() . '\' not found!';
	}

	/**
	 * 获取公共静态资源目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	*/
	public function getResourcePath(string $index) : string
	{
		return RESOURCE_PATH . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * 获取Application局部静态资源目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	*/
	public function getStaticPath(string $index) : string
	{
		return $this->getViewPath('static') . DIRECTORY_SEPARATOR . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * 返回Views(V)显示层的路径
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @param  string      $index      文件/文件夹索引
	 * @param  bool        $selectMode 选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return string
	*/
	final public function getViewPath(string $index, bool $selectMode = true) : string
	{
		return $this->getApp()::getAppPath($selectMode) . "view" . DIRECTORY_SEPARATOR . $index;
	}

	/**
	 * 判断是否存在一个View(V)目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return boolean
	*/
	final public function hasViewPath(string $index) : bool
	{
		$index = explode("/", $index)[0] ?? $index;
		return is_dir($this->getViewPath($index)) || is_file($this->getViewPath($index));
	}

	/**
	 * 返回控制器类名
	 *
	 * @author HanskiJay
	 * @since  2021-02-09
	 * @return string
	 */
	final public function getName() : string
	{
		return Helper::getShortClassName($this);
	}

	/**
	 * 返回对应的App
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @return AppBase
	*/
	final public function getApp() : AppBase
	{
		return $this->app;
	}
}
?>