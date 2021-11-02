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
namespace owoframe\http\route;

use Closure;
use ReflectionClass;

use owoframe\MasterManager;
use owoframe\application\AppManager;
use owoframe\event\http\PageErrorEvent;
use owoframe\exception\InvalidRouterException;
use owoframe\http\HttpManager as Http;
use owoframe\utils\DataEncoder;

final class Router
{

	/**
	 * 路由全路径
	 *
	 * @access private
	 * @var string
	 */
	private static $_pathInfo = null;



	/**
	 * 分发路由
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return void
	 */
	public static function dispatch() : void
	{
		// Closure Method for throw or display an error;
		$internalError = function(string $errorMessage, string $title, string $outputMessage, int $statusCode = 404) : void {
			Http::setStatusCode($statusCode);
			if(DEBUG_MODE) {
				throw new InvalidRouterException($errorMessage);
			} else {
				if(strlen($title) > 0) {
					PageErrorEvent::$title  = $title;
				}
				if(strlen($outputMessage) > 0) {
					PageErrorEvent::$output = $outputMessage;
				}
				MasterManager::getInstance()->getManager('event')->trigger(new PageErrorEvent());
				exit;
			}
		};

		$pathInfo = static::getParameters(-1);
		$appName  = array_shift($pathInfo);
		// Check the valid of the name;
		if(is_null($appName) || !DataEncoder::isOnlyLettersAndNumbers($appName)) {
			$appName = DEFAULT_APP_NAME;
		}
		$appName = strtolower($appName);

		// Judge whether the Application is in the banned list;
		if(in_array($appName, DENY_APP_LIST)) {
			Http::setStatusCode(403);
			return;
		}

		$app = AppManager::getApp($appName);
		if($app === null) {
			$internalError('Cannot find any valid Application!', '', 'Invalid route URL!');
		}
		// Write appName in an anonymous class;
		$anonymousClass = static::getAnonymousClass();
		$anonymousClass->appName = $appName;

		// Judge $pathInfo for ControllerName and RequestMethodName;
		if(count($pathInfo) === 0) {
			$requestMethod = $controllerName = ucfirst($appName);
		}
		elseif(count($pathInfo) >= 1) {
			$controllerName = array_shift($pathInfo);
			// Judge whether the path is legal;
			if(!DataEncoder::isOnlyLettersAndNumbers($controllerName)) {
				$controllerName = $appName;
			}
			// Because until this line of IF-ELSE statement counts the result of $pathInfo equal to 1;
			$requestMethod = $controllerName;

			// If $pathInfo still exceeds 1 parameter;
			if(count($pathInfo) >= 1) {
				$requestMethod = array_shift($pathInfo);
				// Judge whether the path is legal;
				if(!DataEncoder::isOnlyLettersAndNumbers($requestMethod)) {
					$requestMethod = $controllerName;
				}

				// Check the url validity;
				$urlRule = new UrlRule(implode('/', $pathInfo));
				if(!$urlRule->checkValid()) {
					$internalError('Illegal Url requested!', '502 BAD GATEWAY', 'Illegal Url requested!', 403);
				}
			}
		}

		// Initialize Controller;
		if(!($controller = $app->getController($controllerName))) {
			$controller = $app->getDefaultController();
		}
		// If not found any valid Controller;
		if(!$controller) {
			$internalError("Cannot find a valid Controller of Application [{$appName}]!", '', 'The requested Controller was not found!');
		}
		$anonymousClass->controllerName = $controllerName;

		// Start to instance a Controller;
		$reflect = new ReflectionClass($controller);
		// Check RequestMethod validity;
		if($reflect->hasMethod($requestMethod) && $reflect->getMethod($requestMethod)->isPublic()) {
			if(!$app->isControllerMethodBanned($requestMethod, $controller->getName())) {
				$callback = [$controller, $requestMethod];
			} else {
				$internalError("Requested method '{$requestMethod}' is banned, cannot be requested!", '403 Forbidden', 'Permission Denied!', 403);
			}
		} else {
			// If RequestMethod is invalid, then use the alternative methodName;
			$requestMethod = $controller::$autoInvoke_methodNotFound;
			// If the alternative method is the same invalid;
			if(!$reflect->hasMethod($requestMethod)) {
				$internalError("Requested method '{$requestMethod}' is invalid, cannot be requested!", '403 Forbidden', 'Unknown Error happened :(', 403);
			} else {
				$callback = [$controller, $requestMethod];
			}
		}
		$anonymousClass->methodName = $requestMethod;

		$response = Http::Response($callback);
		$response->sendResponse();
	}



	/**
	 * 设置全路径
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @param  string      $pathInfo 路径
	 * @return void
	 */
	public static function setPathInfo(string $pathInfo = '/') : void
	{
		static::$_pathInfo = trim($pathInfo);
	}

	/**
	 * 检查全路径是否为空
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return boolean
	 */
	public static function isEmptyPathInfo() : bool
	{
		return static::$_pathInfo === null;
	}

	/**
	 * 获取全路径
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return string
	 */
	public static function getPathInfo() : string
	{
		if(static::isEmptyPathInfo()) {
			static::setPathInfo(str_replace(server('SCRIPT_NAME'), '', server('REQUEST_URI')));
			// ↓ Double check & set to '/' when last sentence does not work;
			if(static::$_pathInfo === '') static::setPathInfo();
		}
		return static::$_pathInfo;
	}

	/**
	 * 获取路径参数传递
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @param  int      $getFrom 从第几个参数开始获取
	 *                       1:       返回 ApplicationName 之后的参数;
	 *                       2:       返回 ControllerName 之后的参数;
	 *                       3:       返回 RequestMethodName 之后的参数;
	 * @return array
	 */
	public static function getParameters(int $getFrom = 1) : array
	{
		#
		# URI->@/index.php/{ApplicationName}/{ControllerName}/{RequestMethodName}/[GET]...
		#
		$param = array_filter(explode('/', static::getPathInfo()));
		if(($getFrom >= 1) && ($getFrom <= 3)) {
			return array_slice($param, $getFrom);
		} else {
			return $param;
		}
	}


	/**
	 * 返回当前路由选取名称
	 *
	 * @author HanskiJay
	 * @since  2021-11-01
	 * @param  string      $nameType 名称类型
	 * @return mixed
	 */
	public static function getCurrent(string $nameType)
	{
		$closure = self::getAnonymousClass();
		switch($nameType) {
			default:
				return null;

			case 'appName':
			case 'applicationName':
				return $closure->appName;

			case 'cName':
			case 'controllerName':
				return $closure->controllerName;

			case 'mName':
			case 'methodName':
				return $closure->methodName;

			case 'app':
			case 'application':
				return AppManager::getApp($closure->appName);

			case 'controller':
				return AppManager::getApp($closure->appName)->getController($closure->controllerName);
		}
	}

	/**
	 * 创建或返回一个匿名类
	 *
	 * @author HanskiJay
	 * @since  2021-11-01
	 * @return class@anonymous
	 * @access private
	 */
	private static function getAnonymousClass()
	{
		static $anonymousClass;
		if(is_null($anonymousClass)) {
			$anonymousClass = new class()
			{
				public function __set(string $name, $value)
				{
					$this->{$name} = $value;
				}

				public function __get(string $name)
				{
					return $this->{$name} ?? null;
				}

				public function __unset(string $name)
				{
					if(isset($this->{$name})) {
						unset($this->{$name});
					}
				}
			};
		}
		return $anonymousClass;
	}
}