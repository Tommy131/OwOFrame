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
namespace owoframe\http;

use JsonSerializable;
use owoframe\contract\MIMETypeConstant;
use owoframe\exception\JSONException;
use owoframe\http\HttpManager;
use owoframe\utils\DataEncoder;

class Response
{
	/* @int HTTP响应代码(Default:200) */
	protected $code = 200;
	/* @array HTTP header参数设置 */
	protected $header = 
	[
		'Content-Type' => 'text/html; charset=utf-8'
	];
	private $hasSent = false;


	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @method      sendResponse
	 * @description 发送响应头
	 * @author      HanskiJay
	 * @doenIn      2021-02-09
	 * @return      void
	 */
	public function sendResponse() : void
	{
		$call = call_user_func($this->callback);

		if(($this->callback[0] instanceof JsonSerializable)) {
			if(is_array($call)) {
				$call = new DataEncoder($call);
				$call = $call->encode();
			}
			$this->header['Content-Type'] = MIMETypeConstant::MIMETYPE['json'];
		}
		
		if(is_string($call)) {
			echo $call;
		}
		// TODO: 添加一个回调钩子;
		if(!headers_sent() && !empty($this->header)) {
			foreach($this->header as $name => $val) {
				header($name . (!is_null($val) ? ": {$val}"  : ''));
			}
			HttpManager::setStatusCode($this->code);
		}
		if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		$this->hasSent = true;
	}

	/**
	 * @method      setResponseCode
	 * @description 设置HTTP响应代码
	 * @description Set the response code for HTTP;
	 * @param       int[code|响应代码]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function setResponseCode(int $code) : bool
	{
		if(!isset(Helper::HTTP_CODE[$code])) return false;
		$this->code = $code;
		return true;
	}

	/**
	 * @method      getResponseCode
	 * @description 获取当前设置的HTTP响应代码
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
	 * @description 设置HTTP_HEADER
	 * @param       string[index|文件/文件夹索引]
	 * @param       string[val|值]
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function &header(string $name, string $val = '')
	{
		if(($name === "") && ($val === "")) return $this->header;
		elseif(isset($this->header[$name]) && ($val === '')) return $this->header[$name];
		return $this->header[$name] = $val;
	}

	/**
	 * @method      hasSent
	 * @description 返回响应状态
	 * @author      HanskiJay
	 * @doenIn      2021-03-21
	 * @return      boolean
	 */
	public function hasSent() : bool
	{
		return $this->hasSent;
	}
}