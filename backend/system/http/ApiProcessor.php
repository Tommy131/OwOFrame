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

namespace backend\system\http;

use backend\OwOFrame;
use backend\system\exception\JSONException;

abstract class ApiProcessor
{
	/* @array 请求参数 */
	protected $request = [];
	/* @array URL路径解析参数组 */
	protected $path = [];

	/**
	 * @method      filter
	 * @description 过滤请求参数
	 * @author      HanskiJay
	 * @param       array[request|请求参数]
	 * @doenIn      2021-02-06
	 * @return      void
	 */
	public function filter(array $request) : void
	{
		$this->request = $request;
	}

	/**
	 * @method      get
	 * @description 获取请求参数
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       string[searchIndex|搜索索引]
	 * @param       string[mode|选择模式]
	 * @param       mixed[default|默认返回值]
	 * @return      mixed
	 */
	public function get(string $searchIndex, string $mode = 'get', $default = null)
	{
		$mode = in_array($mode, ['get', 'post']) ? $mode : 'get';
		return $this->request[$mode][$searchIndex] ?? $default;
	}

	/**
	 * @method      setPathParam
	 * @description 设置URL路径访问解析参数组
	 * @author      HanskiJay
	 * @doenIn      2021-02-09
	 * @param       array[path|参数组]
	 */
	public function setPathParam(array $path) : void
	{
		$this->path = $path;
	}


	public function getPath() : array
	{
		return $this->path;
	}

	/**
	 * @method      start
	 * @description 启动API处理器进行流程
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       object[response|Response实例]
	 * @return      void
	 */
	public function start(Response $response) : void
	{
		
	}

	/**
	 * @method      requestDenied
	 * @description 请求方法被拒绝的时候自动执行这个方法
	 * @author      HanskiJay
	 * @doenIn      2021-02-07
	 * @return      string
	 */
	public function requestDenied() : string
	{
		return 'Your HTTP requested mode is illegal for this API-Processor.';
	}

	/**
	 * @method      getOutput
	 * @description 返回处理结果
	 * @author      HanskiJay
	 * @doenIn      2021-02-09
	 * @return      string
	 */
	abstract public function getOutput() : string;

	/**
	 * @method      getName
	 * @description 返回Api处理器的名称
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @return      string
	 */
	abstract public static function getName() : string;

	/**
	 * @method      getVersion
	 * @description 返回API处理器版本
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @return      string
	 */
	abstract public static function getVersion() : string;

	/**
	 * @method      mode
	 * @description Api处理器的允许请求方法
	 * @description -1: 接受所有请求    | Accept all all http mode
	 * @description 0:  仅GET请求       | Only Get Request allowed
	 * @description 1:  仅POST请求      | Only POST Request allowed
	 * @description 2:  仅POST请求      | Only POST Request allowed
	 * @description 3:  仅AJAX请求      | Only Ajax Request allowed
	 * @description 4:  AJAX + GET请求  | GET + Ajax Request allowed
	 * @description 5:  AJAX + POST请求 | GET + Ajax Request allowed
	 * @author      HanskiJay
	 * @doenIn      2021-02-07
	 * @return      int
	 */
	abstract public static function mode() : int;
}