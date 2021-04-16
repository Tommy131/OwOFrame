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
use ReflectionClass;

use owoframe\contract\MIMETypeConstant;
use owoframe\contract\StandardOutput;
use owoframe\exception\JSONException;
use owoframe\http\HttpManager;
use owoframe\utils\DataEncoder;

use owoframe\event\http\{BeforeResponseEvent, AfterResponseEvent};
use owoframe\event\system\OutputEvent;

class Response
{
	/* @bool 响应 & 数据发送状态 */
	private $hasSent = false;
	/* @array 回调参数(可以输出数据的回调方法) */
	private $callback;

	/* @int HTTP响应代码(Default:200) */
	protected $code = 200;
	/* @array HTTP header参数设置 */
	protected $header = 
	[
		'Content-Type' => 'text/html; charset=utf-8'
	];


	public function __construct(callable $callback, array $params = [])
	{
		$this->callback   = $callback;
		$this->callParams = $params;
	}

	/**
	 * @method      setCallback
	 * @description 设置回调
	 * @author      HanskiJay
	 * @doenIn      2021-04-16
	 * @param       callable    $callback 回调方法
	 * @param       array       $params   回调参数传递
	 * @return      object@Response
	 */
	public function setCallback(callable $callback, array $params = []) : Response
	{
		$this->__construct($callback, $params);
		return $this;
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
		$eventManager = \owoframe\MasterManager::getInstance()->getManager('event');
		$eventManager->trigger(BeforeResponseEvent::class, [$this]);

		$called = call_user_func_array($this->callback, $this->callParams);

		if(is_array($called)) {
			if($this->callback[0] instanceof DataEncoder) {
				$called = $called->encode();
			}
			elseif($this->callback[0] instanceof JsonSerializable) {
				$called = json_encode($called);
			}
			$this->header('Content-Type', MIMETypeConstant::MIMETYPE['json']);
		}

		if(!is_string($called)) {
			$reflect = new ReflectionClass($this->callback[0]);
			if($reflect->implementsInterface(StandardOutput::class)) {
				$called = $this->callback[0]->getOutput();
			} else {
				$called = new DataEncoder();
				$called->setStandardData(403, '[OwOResponseError] Cannot callback method ' . get_class($this->callback[0]) . '::' . $this->callback[1] . ' for response! (Method must be return string, ' . gettype($called) . ' is returned!', false);
				$called = $called->encode();
				$this->header('Content-Type', MIMETypeConstant::MIMETYPE['json']);
			}
		}
		
		if(!headers_sent() && !empty($this->header)) {
			foreach($this->header as $name => $val) {
				header($name . (!is_null($val) ? ": {$val}"  : ''));
			}
			HttpManager::setStatusCode($this->code);
		}

		$event = new OutputEvent($called);
		$eventManager->trigger($event);
		$event->output();
		unset($event);

		if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		$eventManager->trigger(AfterResponseEvent::class, [$this]);
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
		if(($name === '') && ($val === '')) {
			return $this->header;
		}
		elseif(isset($this->header[$name])) {
			$splitStr = '@';
			$vars     = explode($splitStr, $val);
			$val      = array_shift($vars);
			$mode     = strtolower(array_shift($vars) ?? 'set');
			if(($mode === 'set') || ($mode === 'update')) {
				$this->header[$name] = $val;
				return $this->header[$name];
			} else {
				return $this->header[$name];
			}
		} else {
			$this->header[$name] = $val;
			return $this->header[$name];
		}
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