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

use ReflectionMethod;
use owoframe\MasterManager;
use owoframe\application\AppManager;
use owoframe\helper\{BootStraper, Helper};
use owoframe\http\{HttpManager, Response};
use owoframe\exception\{InvalidControllerException, MethodMissedException, RouterException, UnknownErrorException};

final class Router
{
	/* @string 路由全路径 */
	private static $_pathInfo = null;


	/**
	 * @method      dispath
	 * @description 分发路由
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function dispath() : void
	{
		$pathInfo      = array_filter(explode("/", self::getPathInfo()));
		$primaryParser = array_shift($pathInfo); // 默认设置为App名称 | Default is to set for AppName;
		$primaryParser = !empty($primaryParser) ? strtolower($primaryParser) : null;
		if(empty($primaryParser)) {
			$primaryParser = DEFAULT_APP_NAME;
			self::setPathInfo(DEFAULT_APP_NAME);
		}

		if(is_file($file = FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'router.php')) {
			include_once($file);
			$nrules = RouteRule::getNormalRules();
			$drules = RouteRule::getDomainRules();
			foreach($drules as $domain => $v) {
				if(RouteRule::compareDomain(server('HTTP_HOST'), $domain)) {
					$primaryParser = $v;
					break;
				}
			}
		}

		// 判断当前路由是否为API实例;
		if(!is_null($primaryParser) && (strtolower($primaryParser) === 'api')) {
			$api = strtolower(array_shift($pathInfo) ?? 'unknown');
			if(($api = RouteRule::getApiProcessor($api)) !== null) {
				if(($api::mode() !== -1) && (requestMode() !== $api::mode())) {
					$response = self::Response([$api, 'requestDenied']);
				} else {
					$response = self::Response([$api, 'getOutput']);
					$api->filter(HttpManager::getMerge());
					$api->start($response);
				}
				$response->sendResponse();
				exit;
			}
		}

		/* ---START--- App Running Area */

		// Check App Validation;
		if(($app = AppManager::getApp($primaryParser ?? DEFAULT_APP_NAME)) === null) {
			$app = AppManager::getDefaultApp();
			if($app === null) {
				throw new RouterException("Cannot find any valid Application!");
			}
		}
		
		if(in_array($app->getName(), DENY_APP_LIST)) {
			HttpManager::setStatusCode(404);
			MasterManager::getInstance()->stop();
			return;
		}

		// [0] 若存在HTTP_GET请求时, 通过解析全路径并通过解析的数量来判断正确的Method;
		// [1] 若Url参数信息 ≥ 2时, 将 $pathInfo[1] 设为控制器名称, $pathInfo[2] 设为请求方法;
		// [2] 若Url参数信息 ≥ 1时, 将 $pathInfo[1] 设为控制器名称及请求方法;
		// [3] 若参数为空时, 则将控制器名称设为上方解析的App名称;
		/*if(($controller = $app->matchUrl(self::getCompleteUrl(), $matched)) !== null) {
			$controllerName = $matched;
			$method         = array_shift($pathInfo);
			$method         = !empty($primaryParser) ? $method : strtolower($method);
			var_dump($controllerName, $method);
		}
		else*/
		// @Comment: 这一步防止进行HTTP_GET请求时将参数传入Url, 与Url请求的Method识别方法冲突;
		if(!is_bool(@end(self::getParameters(-1))) && !empty(get(owohttp))) {
			$controllerName = ucfirst(strtolower(array_shift($pathInfo) ?? DEFAULT_APP_NAME));
			switch(count(self::getParameters())) {
				default:
				case 0:
					$method = $controllerName;
				break;

				case 1:
					$controllerName = $method = $primaryParser;
				break;

				case 2:
					$method = self::getParameters()[0];
				break;

				case 3:
					$method = self::getParameters()[1];
				break;

				case 4:
					$method = self::getParameters()[2];
				break;
			}
		}
		elseif(count($pathInfo) >= 2) {
			$controllerName = ucfirst(strtolower(array_shift($pathInfo)));
			$method         = strtolower(array_shift($pathInfo));
		}
		elseif(count($pathInfo) >= 1) {
			$controllerName = ucfirst(strtolower(array_shift($pathInfo)));
		} else {
			if(!isset($controllerName) && ($app->getDefaultController() !== null) && ($primaryParser === $app->getName())) {
				$controllerName = $app->getDefaultController(true);
			} else {
				$controllerName = ucfirst($primaryParser ?? DEFAULT_APP_NAME);
			}
		}

		/* 
		 * 我知道这样写非常不可读 + 不利于维护... 但是这样是最节省行数的一种写法了. 说一下作用:
		 *  ┌─ 先通过上方代码段解析到的控制器名称 $controllerName 获取控制器; 如果控制器为空, 则继续判断:
		 *  │ ┌─ 通过最开始解析得到的一级解析 $primaryParser 获取控制器, 如果控制器存在, 则返回 $controller1 给 $controller (此时$controller === $controller1);
		 *  │ └─ 如果控制器为空, 则返回 null
		 *  └─ 否则返回最开始获取到的控制器 $controller (此时$controller === $controller);
		 * 如果还是看不懂请找我;
		 * English version please tranlate it by yourself (my English is poor xD);
		*/
		if(!isset($controller)) {
			$controller = (($controller = $app->getController($controllerName)) === null)
						? ((($controller1 = $app->getController($primaryParser)) !== null)
							? $controller1
							: null)
							// : $app->getDefaultController())
						: $controller;
		}
		if($controller === null) {
			if(defined('DEBUG_MODE') && DEBUG_MODE) {
				throw new InvalidControllerException($app->getName(), $controllerName, get_class($app));
			} else {
				if($app->autoTo404Page()) {
					$app->renderPageNotFound();
				}
				HttpManager::setStatusCode(404);
			}
			// Stop continue routing while the Router doesn't match a valid Page Controller;
			MasterManager::getInstance()->stop();
			return;
		} else {
			if($controller instanceof \Closure) {
				$controller(new self);
				MasterManager::getInstance()->stop();
			} else {
				$app->setParameters($pathInfo);

				$method = $method ?? $controllerName;
				if(!method_exists($controller, $method)) {
					if(!method_exists($controller, $controller::$methodNotFound_DefaultMethod)) {
						throw new MethodMissedException(get_class($controller), $method);
					} else {
						$method = $controller::$methodNotFound_DefaultMethod;
					}
				}
				if(!(new ReflectionMethod($controller, $method))->isPublic()) {
					$method = $controller::$methodNotFound_DefaultMethod;
				}
				// input callable arg;
				self::Response([$controller, $method])->sendResponse();
				if(!empty($controller::$goto)) {
					header('Refresh:3; url='.self::getRootUrl() . $controller::$goto);
				}
				
			}
		}

		/* ----END---- App Running Area */


		if(defined('DEBUG_MODE') && DEBUG_MODE && $controller::$showUsedTimeDiv) {
			$runTime = BootStraper::getRunTime();
			echo
<<<EOF
<div style="position: absolute; z-index: 999999; bottom: 0; right: 0; margin: 5px; padding: 5px; background-color: #aaaaaa; border-radius: 5px;">
	<div>UsedTime: <b>{$runTime}s</b></div>
</div>
EOF;
		}
	}

	public static function Response(callable $callback) : Response
	{
		static $response;
		if(!$response instanceof Response) {
			$response = new Response($callback);
		}
		return $response;
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
		return (NULL === self::$_pathInfo);
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
		if(self::isEmptyPathInfo())
		{
			self::setPathInfo(str_replace(server('SCRIPT_NAME'), "", server('REQUEST_URI')));
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
		$param = array_filter(explode("/", self::getPathInfo()));
		if($getFrom === 1) { // 返回AppName之后的参数;
			return array_slice($param, 1);
		}
		elseif($getFrom === 2) { // 返回ControllerName之后的参数;
			return array_slice($param, 2);
		}
		elseif($getFrom === 3) { // 返回MethodName之后的参数;
			return array_slice($param, 3);
		} else {
			return $param;
		}
	}

	/**
	 * @method      isSecure
	 * @description 判断是否为HTTPS协议
	 * @description Check if HTTP Protocol has used SSL
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function isSecure() : bool
	{
		return (!empty(server('HTTPS')) && 'off' != strtolower(server('HTTPS'))) 
			|| (!empty(server('SERVER_PORT')) && 443 == server('SERVER_PORT'));
	}

	/**
	 * @method      getCompleteUrl
	 * @description 获取完整请求HTTP地址
	 * @description Get complete http requested url
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getCompleteUrl() : string
	{
		return server('REQUEST_SCHEME').'://'.server('HTTP_HOST').server('SCRIPT_NAME').server('REQUEST_URI');
	}

	/**
	 * @method      getRootUrl
	 * @description 获取根地址
	 * @description Get root url
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getRootUrl() : string
	{
		return server('REQUEST_SCHEME').'://'.server('HTTP_HOST');
	}

	/**
	 * @method      betterUrl
	 * @description 返回自定义Url
	 * @description Set HTTP_HEADER;
	 * @param       string[name|名称]
	 * @param       string[path|路径]
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function betterUrl(string $name, string $path) : string
	{
		return trim($path, '/').'/'.str_replace('//', '/', ltrim(((0 === strpos($name, './')) ? substr($name, 2) : $name), '/'));
	}
}