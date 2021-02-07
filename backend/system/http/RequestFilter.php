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

class RequestFilter
{

	/**
	 * @method      getMerge
	 * @description 返回整个的请求数据(默认返回原型)
	 * @author      HanskiJay
	 * @doenIn      2021-02-06
	 * @param       callable|null[callback|回调参数]
	 * @return      array(开发者需注意在此返回参数时必须使回调参数返回数组)
	 */
	public static function getMerge(?callable $callback = null) : array
	{
		$array = ['get' => get(owohttp), 'post' => post(owohttp)];
		return !is_null($callback) ? call_user_func_array($callback, $array) : $array;
	}
}