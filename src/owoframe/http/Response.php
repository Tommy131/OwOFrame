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
namespace owoframe\http;

use JsonSerializable;
use ReflectionClass;

use owoframe\constant\MIMETypeConstant;
use owoframe\constant\StandardOutputConstant;

use owoframe\helper\BootStrapper;
use owoframe\helper\Helper;

use owoframe\http\HttpManager;

use owoframe\utils\DataEncoder;

use owoframe\event\http\{BeforeResponseEvent, AfterResponseEvent};
use owoframe\event\system\OutputEvent;

class Response
{
	/**
	 * 响应 & 数据发送状态
	 *
	 * @access private
	 * @var boolean
	 */
	private $hasSent = false;

	/**
	 * 回调参数(可以输出数据的回调方法)
	 *
	 * @access private
	 * @var callable
	 */
	private $callback;

	/**
	 * HTTP响应代码(Default:200)
	 *
	 * @access protected
	 * @var integer
	 */
	protected $code = 200;

	/**
	 * HTTP header参数设置
	 *
	 * @access protected
	 * @var array
	 */
	protected $header =
	[
		'Content-Type'           => 'text/html; charset=utf-8',
		'X-Content-Type-Options' => 'nosniff',
		'Pragma'                 => 'HTTP/1.0'
	];

	/**
	 * 默认响应信息
	 *
	 * @var string
	 */
	public $defaultResponseMsg = '[OwOResponseError] Keine Ahnung...';



	public function __construct(?callable $callback, array $params = [])
	{
		$this->callback   = $callback;
		$this->callParams = $params;
	}

	/**
	 * 设置回调
	 *
	 * @author HanskiJay
	 * @since  2021-04-16
	 * @param  callable   $callback 可回调参数
	 * @param  array      $params   回调参数传递
	 * @return Response
	 */
	public function setCallback(callable $callback, array $params = []) : Response
	{
		$this->__construct($callback, $params);
		return $this;
	}

	/**
	 * 发送响应头
	 *
	 * @author HanskiJay
	 * @since  2021-02-09
	 * @return void
	 */
	public function sendResponse() : void
	{
		$eventManager = \owoframe\MasterManager::getInstance()->getManager('event');
		$eventManager->trigger(BeforeResponseEvent::class, [$this]);

		// If the callback is invalid;
		if(!is_callable($this->callback)) {
			$this->callback = [$this, 'defaultResponseMsg'];
		}
		// Callback method and get result;
		$called = call_user_func_array($this->callback, $this->callParams);

		// If result is an Array;
		if(is_array($called)) {
			if($this->callback[0] instanceof DataEncoder) {
				$called = $this->callback[0]->encode();
			} else {
				$called = json_encode($called);
			}
			$this->header('Content-Type', MIMETypeConstant::MIMETYPE['json']);
			$isJson = true;
		}

		// Check whether the callback result type is String;
		if(!is_string($called)) {
			$reflect = new ReflectionClass($this->callback[0]);
			if($reflect->implementsInterface(StandardOutputConstant::class)) {
				$called = $this->callback[0]->getOutput();
			} else {
				$called = new DataEncoder();
				$called->setStandardData(502, '[OwOResponseError] Cannot callback method ' . get_class($this->callback[0]) . '::' . $this->callback[1] . ' for response! (Method must be return string, ' . gettype($called) . ' is returned!', false);
				$called = $called->encode();
				$this->header('Content-Type', MIMETypeConstant::MIMETYPE['json']);
				$isJson = true;
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
		if(!isset($isJson)) {
			self::getRunTimeDiv();
		}

		if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		$eventManager->trigger(AfterResponseEvent::class, [$this]);
		$this->hasSent = true;
	}

	/**
	 * 设置HTTP响应代码
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @param  int      $code 响应代码
	 * @return boolean
	 */
	public function setResponseCode(int $code) : bool
	{
		if(!isset(Helper::HTTP_CODE[$code])) return false;
		$this->code = $code;
		return true;
	}

	/**
	 * 获取当前设置的HTTP响应代码
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @param  int      $code 响应代码
	 * @return int
	 */
	public function getResponseCode(int $code = 403) : int
	{
		return $this->code ?: $code;
	}

	/**
	 * 设置HTTP_HEADER
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @param  string      $index 文件/文件夹索引
	 * @param  string      $val 值
	 * @return mixed
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
	 * 返回响应状态
	 *
	 * @author HanskiJay
	 * @since  2021-03-21
	 * @return boolean
	 */
	public function hasSent() : bool
	{
		return $this->hasSent;
	}

	/**
	 * 默认响应信息
	 *
	 * @author HanskiJay
	 * @since  2021-03-21
	 * @return string
	 */
	public function defaultResponseMsg() : string
	{
		return $this->defaultResponseMsg;
	}

	/**
	 * 输出运行时间框
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @param  boolean      $condition
	 * @return void
	 */
	public static function getRunTimeDiv(bool $condition = true) : void
	{
		if(DEBUG_MODE || $condition) {
			echo str_replace('{runTime}', (string) BootStrapper::getRunTime(), base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB6LWluZGV4OiA5OTk5OTk7IGJvdHRvbTogMDsgcmlnaHQ6IDA7IG1hcmdpbjogNXB4OyBwYWRkaW5nOiA1cHg7IGJhY2tncm91bmQtY29sb3I6ICNhYWFhYWE7IGJvcmRlci1yYWRpdXM6IDVweDsiPgoJPGRpdj5Vc2VkVGltZTogPGI+e3J1blRpbWV9czwvYj48L2Rpdj4KPC9kaXY+'));
		}
	}
}