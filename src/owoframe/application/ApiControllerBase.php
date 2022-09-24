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

use owoframe\http\HttpManager;
use ReflectionMethod;

abstract class ApiControllerBase extends ControllerBase
{

	/**
	 * 检测API版本控制的开关
	 *
	 * @access protected
	 * @var boolean
	 */
    protected $withVersionControl = true;

	/**
	 * 初始化方法
	 *
	 * @return mixed
	 */
    public function init()
	{
		$params = HttpManager::getParameters(2);
	    if($this->withVersionControl) {
		    if(empty($params) || !$this->checkApiVersionValidity(array_shift($params))) {
			    return $this->responseErrorStatus(403, 'Current requested Api-Version is not allowed');
			}
		}

		$requestMethod = array_shift($params);
	    if(!is_string($requestMethod) || !method_exists($this, $requestMethod) || !$this->isRequestAllowed($requestMethod, $errorMessage)) {
		    return $this->responseErrorStatus(403, $errorMessage ?? 'Access Denied');
		}

		$reflection = new ReflectionMethod($this, $requestMethod);
	    return $reflection->isStatic() ? static::{$requestMethod}() : $this->{$requestMethod}();
	}

	/**
	 * 检测是否允许请求方法
	 *
	 * @param  string  $method
	 * @param  &string $errorMessage
	 * @return boolean
	 */
    public function isRequestAllowed(string $method, &$errorMessage = null) : bool
	{
		$rule = $this->getRules()[$method] ?? null;
	    if(is_null($rule)) {
			$errorMessage = "Method {$method} not found";
		}
	    elseif(stripos(check($method), $rule) !== false) {
			$errorMessage = "Method {$method} should be request in {$rule} mode(s)";
		}
	    return is_null($errorMessage);
	}

	/**
	 * 返回方法允许的请求方式
	 *
	 * @return array
	 */
    abstract public function getRules() : array;
	/* {
	    return [
			'test' => 'get, post'
		];
	} */

	/**
	 * 检查当前请求的路由中的API版本的有效性
	 *
	 * @param  string  $version
	 * @return boolean
	 */
    abstract public function checkApiVersionValidity(string $version) : bool;
	/* {
	    return in_array($version, ['v1']);
	} */
}
?>