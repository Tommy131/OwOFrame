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
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

declare(strict_types=1);
namespace backend\system\app;

use backend\OwOFrame;

abstract class ControllerBase
{
	/* @AppBase 返回AppBase实例 */
	private $app = null;
	/* @bool Front-End开启或关闭UsedTimeDiv(Default:true) */
	public static $showUsedTimeDiv = true;
	/* @int HTTP响应代码(Default:200) */
	protected $code = 200;
	/* @array HTTP header参数设置 */
	protected $header = 
	[
		"Content-Type" => "text/html; charset=utf-8"
		// "Content-Type" => "application/json"
	];
	/* @string 若存在该Url, Router将会在执行完对应请求方法之后跳转到该地址 */
	public static $goto = null;
	/* @string 若请求的Url中包含无效的请求方法, 则默认执行该方法 */
	public static $methodNotFound_DefaultMethod = 'methodNotFound';


	public function __construct(AppBase $app)
	{
		$this->app = $app;
	}

	/**
	 * @method      methodNotFound
	 * @description 这只是一个示例, 参考上方注释;
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-10-08 22:04
	*/
	public function methodNotFound()
	{
		return 'Requested method not found!';
	}

	/**
	 * @method      getApp
	 * @description 返回对应的App;
	 * @description Return corresponding AppBase Object;
	 * @return      AppBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	final public function getApp() : AppBase
	{
		return $this->app;
	}

	/**
	 * @method      getCommonPath
	 * @description 获取静态资源目录;
	 * @param       string[index|文件/文件夹索引]
	 * @return      string
	 * Author:      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function getCommonPath(string $index) : string
	{
		return __BACKEND__ . 'common' . DIRECTORY_SEPARATOR . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getViewPath
	 * @description 返回Views(V)显示层的路径;
	 * @description Get the path for View(V) relativly;
	 * @param       string[index|文件/文件夹索引]
	 * @param       bool[selectMode|选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return      null or string
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	final public function getViewPath(string $index, bool $selectMode = true) : ?string
	{
		return (($selectMode) ? $this->getApp()->getAppPath() : $this->getApp()->getAppPath($selectMode)) . "view" . DIRECTORY_SEPARATOR . $index ?? null;
	}

	/**
	 * @method      hasViewPath
	 * @description 判断是否存在一个View(V)目录;
	 * @description Determine whether there is a Views(V) directory;
	 * @param       string[index|文件/文件夹索引]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	final public function hasViewPath(string $index) : bool
	{
		$index = explode("/", $index)[0] ?? $index;
		return is_dir(self::getViewPath($index)) || is_file(self::getViewPath($index));
	}

	/**
	 * @method      setResponseCode
	 * @description 设置HTTP响应代码;
	 * @description Set the response code for HTTP;
	 * @param       int[code|响应代码]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function setResponseCode(int $code) : bool
	{
		if(!isset(OwOFrame::HTTP_CODE[$code])) return false;
		$this->code = $code;
		return true;
	}

	/**
	 * @method      getResponseCode
	 * @description 获取当前设置的HTTP响应代码;
	 * @description Get the response code from HTTP;
	 * @param       int[code|响应代码](Default:403)
	 * @return      int
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function getResponseCode(int $code = 403) : int
	{
		return $this->code ?: $code;
	}

	/**
	 * @method      header
	 * @description 设置HTTP_HEADER;
	 * @description Set HTTP_HEADER;
	 * @param       string[index|文件/文件夹索引]
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function header(string $name, string $val = "")
	{
		if(($name === "") && ($val === "")) return $this->header;
		elseif(isset($this->header[$name])) return $this->header[$name];
		$this->header[$name] = $val;
	}

	/**
	 * @method      callback
	 * @description 控制器结束后的回调函数;
	 * @description Set HTTP_HEADER;
	 * @param       string[index|文件/文件夹索引]
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function callback(string $method = "")
	{
		if(($method !== "") && method_exists($this, $method)) {
			$result = $this->{$method}();
		}
		if(!headers_sent() && !empty($this->header))
		{
			foreach($this->header as $name => $val) {
				header($name . (!is_null($val) ? ':' . $val : ''));
			}
			http_response_code($this->code);
		}
		if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		return $result ?? '';
	}

	/**
	 * @method      callback
	 * @description 控制器结束后的回调函数;
	 * @description Set HTTP_HEADER;
	 * @param       string[index|文件/文件夹索引]
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function url(string $name, string $path) : string
	{
		return trim($path, '/').'/'.str_replace('//', '/', ltrim(((0 === strpos($name, './')) ? substr($name, 2) : $name), '/'));
	}

	public function getName() : string
	{
		return OwOFrame::getShortClassName($this);
	}
}
?>