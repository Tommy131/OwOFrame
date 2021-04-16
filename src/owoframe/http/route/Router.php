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
namespace owoframe\http\route;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use owoframe\application\AppBase;
use owoframe\application\AppManager;
use owoframe\helper\{BootStraper, Helper};
use owoframe\http\HttpManager as Http;
use owoframe\http\Response;
use owoframe\exception\{InvalidControllerException, MethodMissedException, InvalidRouterException, UnknownErrorException};
use owoframe\utils\DataEncoder;

final class Router
{
	/* @string 路由全路径 */
	private static $_pathInfo = null;

	/* object@AppBase 当前Application实例 */
	private static $currentApp;


	/**
	 * @method      dispath
	 * @description 分发路由
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function dispath() : void
	{
		$tmps = [];
		$pathInfo = self::getParameters(-1);
		$appName  = array_shift($pathInfo); // 默认设置为App名称 | Default is to set for AppName;
		if(is_null($appName) || !DataEncoder::isOnlyLettersAndNumbers($appName)) {
			$appName = DEFAULT_APP_NAME;
		}
		$appName = strtolower($appName);

		if(is_file($file = FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'router.php')) {
			include_once($file);
			$nrules = RouteRule::getNormalRules();
			$drules = RouteRule::getDomainRules();
			foreach($drules as $domain => $v) {
				if(RouteRule::compareDomain(server('HTTP_HOST'), $domain)) {
					$appName = $v;
					break;
				}
			}
		}

		if(in_array($appName, DENY_APP_LIST)) {
			HttpManager::setStatusCode(404);
			return;
		}

		$app = AppManager::getApp($appName) ?? AppManager::getDefaultApp();
		if($app === null) {
			HttpManager::setStatusCode(404);
			throw new InvalidRouterException("Cannot find any valid Application!");
			// TODO: 增加一个未找到App的回调方法(callback);
		} else {
			self::$currentApp = $app;
		}
		
		// Judgment $pathInfo for ControllerName and RequestMethodName;
		if(count($pathInfo) === 0) {
			$controllerName = $requestMethod = $appName;
		}
		elseif(count($pathInfo) >= 1) {
			$controllerName = array_shift($pathInfo);
			if(!DataEncoder::isOnlyLettersAndNumbers($controllerName)) {
				$controllerName = $appName;
			}
			$requestMethod = $controllerName;

			if(count($pathInfo) >= 1) { // If $pathInfo still more than 2;
				$requestMethod = array_shift($pathInfo);
				if(!DataEncoder::isOnlyLettersAndNumbers($requestMethod)) {
					$requestMethod = $controllerName;
				}

				while(count($pathInfo) > 1) { // The rest vars will be used for GET to Controller;
					$tmps[] = array_shift($pathInfo);
				}
				$rest = array_shift($pathInfo);
				if($rest !== null) {
					if(($s = stripos($rest, '?')) !== false) {
						if($s > 0) {
							$tmps[] = substr($rest, 0, $s);
							$rest   = substr($rest, ++$s);
						}
						$rest = explode('&', substr($rest, 1));
						$_get = [];
						foreach($rest as $var) {
							$var = explode('=', $var);
							$_get[$var[0]] =$var[1];
						}
						array_merge($_GET, $_get);
					}
				}
			}
		}
		$app->setParameters($tmps);

		$controllerName = ucfirst($controllerName);
		if(($controller = $app->getController($controllerName)) === null) {
			$controller = $app->getDefaultController();
		}
		if($controller === null) {
			Http::setStatusCode(404);
			throw new InvalidControllerException($app->getName(), $controllerName);
			// TODO: 增加一个未找到Controller的回调方法(callback);
		}
		if($controller instanceof Closure) {
			$reflect = new ReflectionFunction($controller);
			// TODO;
		} else {
			$reflect = new ReflectionClass($controller);
			if($reflect->hasMethod($requestMethod) && $reflect->getMethod($requestMethod)->isPublic()) {
				$callback = [$controller, $requestMethod];
			} else {
				$requestMethod = $controller::$autoInvoke_methodNotFound;
				if(!$reflect->hasMethod($requestMethod)) {
					if(defined('DEBUG_MODE') && DEBUG_MODE) {
						throw new MethodMissedException(get_class($controller), $requestMethod);
					} else {
						if($app->autoTo404Page()) {
							$callback = [$app, 'renderPageNotFound'];
						}
					}
					// TODO: 增加一个请求方法无效的事件回调;
				}
			}

			$response = Http::Response($callback);
			$response->sendResponse();
		}

		if(defined('DEBUG_MODE') && DEBUG_MODE && $controller::$showUsedTimeDiv) {
			echo str_replace('{runTime}', BootStraper::getRunTime(), base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB6LWluZGV4OiA5OTk5OTk7IGJvdHRvbTogMDsgcmlnaHQ6IDA7IG1hcmdpbjogNXB4OyBwYWRkaW5nOiA1cHg7IGJhY2tncm91bmQtY29sb3I6ICNhYWFhYWE7IGJvcmRlci1yYWRpdXM6IDVweDsiPgoJPGRpdj5Vc2VkVGltZTogPGI+e3J1blRpbWV9czwvYj48L2Rpdj4KPC9kaXY+'));
		}
	}

	/**
	 * @method      getCurrentApp
	 * @description 返回当前Application实例
	 * @author      HanskiJay
	 * @doenIn      2021-04-17
	 * @return      null|object@AppBase
	 */
	public static function getCurrentApp() : ?AppBase
	{
		return self::$currentApp;
	}

	/**
	 * @method      setPathInfo
	 * @description 设置全路径
	 * @description Set HTTP Url Path Info
	 * @param       string[pathInfo|路径]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function setPathInfo(string $pathInfo = '/') : void
	{
		self::$_pathInfo = trim($pathInfo);
	}

	/**
	 * @method      isEmptyPathInfo
	 * @description 检查全路径是否存在
	 * @description Check if PathInfo exists
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function isEmptyPathInfo() : bool
	{
		return self::$_pathInfo === null;
	}

	/**
	 * @method      getPathInfo
	 * @description 获取全路径
	 * @description Get PathInfo
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getPathInfo() : string
	{
		if(self::isEmptyPathInfo()) {
			self::setPathInfo(str_replace(server('SCRIPT_NAME'), '', server('REQUEST_URI')));
			if(self::$_pathInfo === "") self::setPathInfo();
		}
		return self::$_pathInfo;
	}

	/**
	 * @method      getParameters
	 * @description 获取路径参数传递
	 * @description Get PathInfo to array
	 * @param       int[getFrom|从第几个参数开始获取](Default:1)
	 * @return      array
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getParameters(int $getFrom = 1) : array
	{
		#
		# URI->@/index.php/{ApplicationName}/{ControllerName}/{RequestMethodName}/[GET]...
		# 
		$param = array_filter(explode('/', self::getPathInfo()));
		// 1: 返回 ApplicationName 之后的参数;
		// 2: 返回 ControllerName 之后的参数;
		// 3: 返回 RequestMethodName 之后的参数;
		if(($getFrom >= 1) && ($getFrom <= 3)) {
			return array_slice($param, $getFrom);
		} else {
			return $param;
		}
	}
}