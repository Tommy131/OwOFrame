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
	* GitHub: https://github.com/Tommy131
	
************************************************************************/

declare(strict_types=1);
namespace owoframe\application;

use owoframe\helper\Helper;

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
	 * @method      methodNotFound
	 * @description 这只是一个示例, 参考上方注释
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-10-08 22:04
	*/
	public function methodNotFound()
	{
		return 'Requested method not found!';
	}

	/**
	 * @method      getResourcePath
	 * @description 获取公共静态资源目录
	 * Author:      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getResourcePath(string $index) : string
	{
		return RESOURCE_PATH . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getStaticPath
	 * @description 获取Application局部静态资源目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getStaticPath(string $index) : string
	{
		return $this->getViewPath('static') . DIRECTORY_SEPARATOR . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getViewPath
	 * @description 返回Views(V)显示层的路径
	 * @description Get the path for View(V) relativly
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	 * @param       string      $index      文件/文件夹索引
	 * @param       bool        $selectMode 选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return      string
	*/
	final public function getViewPath(string $index, bool $selectMode = true) : string
	{
		return $this->getApp()::getAppPath($selectMode) . "view" . DIRECTORY_SEPARATOR . $index;
	}

	/**
	 * @method      hasViewPath
	 * @description 判断是否存在一个View(V)目录
	 * @description Determine whether there is a Views(V) directory
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      boolean
	*/
	final public function hasViewPath(string $index) : bool
	{
		$index = explode("/", $index)[0] ?? $index;
		return is_dir(self::getViewPath($index)) || is_file(self::getViewPath($index));
	}

	/**
	 * @method      getName
	 * @description 返回控制器类名
	 * @author      HanskiJay
	 * @doenIn      2021-02-09
	 * @return      string
	 */
	final public function getName() : string
	{
		return Helper::getShortClassName($this);
	}

	/**
	 * @method      getApp
	 * @description 返回对应的App
	 * @description Return corresponding AppBase Object
	 * @return      AppBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	final public function getApp() : AppBase
	{
		return $this->app;
	}
}
?>