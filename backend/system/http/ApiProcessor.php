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
	 * @return      mixed
	 */
	public function get(string $searchIndex, string $mode = 'get')
	{
		$mode = in_array($mode, ['get', 'post']) ? $mode : 'get';
		return $this->request[$mode][$searchIndex] ?? null;
	}

	/**
	 * @method      getName
	 * @description 返回Api处理器的名称
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @return      string
	 */
	abstract public function getName() : string;

	/**
	 * @method      start
	 * @description 启动API处理器进行流程
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       array[params|传入的参数]
	 * @return      string
	 */
	abstract public function start(array $params) : string;

	/**
	 * @method      getVersion
	 * @description 返回API处理器版本
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @return      string
	 */
	abstract public function getVersion() : string;
}