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

use ReflectionClass;

use owoframe\MasterManager;
use owoframe\constants\HTTPConstant;
use owoframe\interfaces\Unit;

use owoframe\http\route\DomainRule;
use owoframe\http\route\UrlRule;

use owoframe\event\http\BeforeRouteEvent;
use owoframe\event\http\PageErrorEvent;

use owoframe\exception\InvalidRouterException;

use owoframe\object\INI;
use owoframe\utils\DataEncoder;

class HttpManager implements HTTPConstant, Unit
{
    /**
	 * 默认的用于过滤的正则表达式
	 */
	public const DEFAULT_XSS_FILTER =
	[
		"/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
		"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
		"/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
		// "/(\\\(|\\\)| |\s|!|@|#|\\\$|%|\\\^|&|\\\*|\\\-|_|\\\+|\\\=|\\\||)/isU",
		// "/[`~!@#$%^&*()_\-+=<>?:\\\"{}|,.\/;'\\[\]·~！#￥%……&*（）——\-+={}|《》？：“”【】、；‘'，。、]/im"
	];

	/**
	 * 路由全路径
	 *
	 * @access private
	 * @var string
	 */
	private static $_pathInfo = null;

    /**
     * 日志记录容器实例
     *
     * @var Logger
     */
    private $logger;

    /**
	 * 自定义的用于过滤的正则表达式
	 *
	 * @var array
	 */
	public static $customFilter = [];

	/**
	 * 不记录日志的路由
	 *
	 * @var array
	 */
	public static $notLogUrl = [];


    /**
     * 构造函数
     */
    public function __construct()
    {
		$this->logger = MasterManager::getInstance()->getUnit('logger');
		$this->logger->createLogger('http')->updateConfig('http', ['logPrefix' => 'HTTP/Router']);
		MasterManager::getInstance()->getUnit('event')->trigger(new BeforeRouteEvent());
    }

	public function start() : void
	{
		// Closure Method for throw or display an error;
		$internalError = function(string $errorMessage, string $title, string $outputMessage, int $statusCode = 404) : void {
			if(INI::_global('owo.debugMode', true)) {
				throw new InvalidRouterException($errorMessage);
			} else {
				if(strlen($title) > 0) {
					PageErrorEvent::$title  = $title;
				}
				if(strlen($outputMessage) > 0) {
					PageErrorEvent::$output = $outputMessage;
				}
				PageErrorEvent::$statusCode = $statusCode;
				MasterManager::getInstance()->getUnit('event')->trigger(new PageErrorEvent());
				exit;
			}
		};

		$pathInfo = self::getParameters(-1);
		$appName  = array_shift($pathInfo);

		// Check Domain bind rules;
		include_once(config_path('router.php'));
		if($to = DomainRule::get(server('HTTP_HOST'), $bindType)) {
			switch($bindType) {
				case DomainRule::TAG_BIND_TO_URL:
					$parsed = parse_url($to);
					self::setPathInfo($parsed['path']);
					$pathInfo = self::getParameters(-1);
					$appName  = array_shift($pathInfo);
				break;

				case DomainRule::TAG_BIND_TO_APPLICATION:
					$pathInfo = self::getParameters(-1);
					$appName = $to;
				break;
			}
		}

		// Check the valid of the name;
		if(is_null($appName) || !DataEncoder::isOnlyLettersAndNumbers($appName)) {
			$appName = INI::_global('owo.defaultApp');
		}
		$appName = strtolower($appName);

		// Judge whether the Application is in the banned list;
		if(in_array($appName, explode(',', INI::_global('owo.denyList')))) {
			self::setStatusCode(403);
			return;
		}

		$app = MasterManager::getInstance()->getUnit('app')->getApp($appName);
		if($app === null) {
			$msg = 'Cannot find any valid Application!';
			$this->logger->error('[403] ' . $msg);
			$internalError($msg, '', 'Invalid route URL!');
		}
		if($app::isCLIOnly()) {
			$msg = 'This Application can only run in CLI Mode!';
			$this->logger->error('[403] ' . $msg);
			$internalError($msg, '', 'Application is banned!');
		}
		// Write appName in an anonymous class;
		$anonymousClass = self::getAnonymousClass();
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

				$urlRule = implode('/', $pathInfo);
				// Check the url validity;                              ↓  传入 [RequestMethod] 之后的Url残余   ↓
				$urlRule = isset($customizeUrlRule) ? $customizeUrlRule($urlRule) : new UrlRule($urlRule, UrlRule::TAG_USE_DEFAULT_STYLE);
				if(!$urlRule->checkValid($urlParameters)) {
					$msg = 'Illegal Url requested!';
					$this->logger->error('[502] ' . $msg);
					$internalError($msg, '502 BAD GATEWAY', 'Illegal Url requested!', 403);
				}
				$anonymousClass->urlParameters = $urlParameters;
			}
		}
		$anonymousClass->methodName = $requestMethod;

		// Initialize Controller;
		if(!($controller = $app->getController($controllerName))) {
			$controller = $app->getDefaultController();
		}
		// If not found any valid Controller;
		if(!$controller) {
			$msg = "Cannot find a valid Controller of Application [{$appName}]!";
			$this->logger->error($msg);
			$internalError($msg, '', 'The requested Controller was not found!');
		}
		if($app->isControllerBanned($controllerName)) {
			$msg = "Controller {$controllerName} has been banned from the Application!";
			$this->logger->error($msg);
			$internalError($msg, 'ACCESS FORBIDDEN', 'Request denied (Too low permission)', 403);
		}
		$anonymousClass->controllerName = $controller->getName();

		// Start to instance a Controller;
		$reflect = new ReflectionClass($controller);
		// Check RequestMethod validity;
		if($reflect->hasMethod($requestMethod) && $reflect->getMethod($requestMethod)->isPublic()) {
			if(!$app->isControllerMethodBanned($requestMethod, $controller->getName())) {
				$callback = [$controller, $requestMethod];
			} else {
				$msg = "Requested method '{$requestMethod}' is banned, cannot be requested!";
				$this->logger->error($msg);
				$internalError($msg, '403 Forbidden', 'Permission Denied!', 403);
			}
		} else {
			// If RequestMethod is invalid, then use the alternative methodName;
			$requestMethod = $controller::$autoInvoke_methodNotFound;
			// If the alternative method is the same invalid;
			if(!$reflect->hasMethod($requestMethod)) {
				$msg = "Requested method '{$requestMethod}' is invalid, cannot be requested!";
				$this->logger->error($msg);
				$internalError($msg, '403 Forbidden', 'Unknown Error happened :(', 403);
			} else {
				$callback = [$controller, $requestMethod];
			}
		}

		$anonymousClass->response = self::Response($callback);
		$anonymousClass->response->sendResponse();
	}


	/**
	 * ~ HTTP 参数操作方法
	 */
	/**
	 * 快速新建响应头实例
	 *
	 * @author HanskiJay
	 * @since  2021-03-18
	 * @param  callable|null    $callback 可回调参数
	 * @param  array            $params   回调参数传递
	 * @param  bool             $reload   重新生成响应实例
	 * @return Response
	 */
	public static function Response(?callable $callback = null, array $params = [], bool $reload = false) : Response
	{
		static $response;

		if($reload) {
			$response = null;
		}
		if(!$response instanceof Response) {
			$response = new Response($callback, $params);
		}
		return $response;
	}

	/**
	 * 设置HTTP状态码
	 *
	 * @author HanskiJay
	 * @since  2021-01-10
	 * @param  int      $code 状态码
	 * @return void
	 */
	public static function setStatusCode(int $code) : void
	{
		if(isset(self::HTTP_CODE[$code])) {
			header(((server('SERVER_PROTOCOL') !== null) ? server('SERVER_PROTOCOL') : 'HTTP/1.1') . " {$code} " . self::HTTP_CODE[$code], true, $code);
		}
	}

	/**
	 * 设置自定义的XSS过滤器
	 *
	 * @author HanskiJay
	 * @since  2021-03-07
	 * @param  array       $filter 正则过滤器组
	 * @return void
	 */
	public static function setXssFilter(array $filter) : void
	{
		static::$customFilter = array_merge(static::$customFilter, $filter);
	}

	/**
	 * XSS跨站请求过滤
	 *
	 * @author HanskiJay
	 * @since  2021-02-07
	 * @param  string      $str         需要过滤的参数
	 * @param  string      $allowedHTML 允许的HTML标签 (e.g. "<a><b><div>" (将不会过滤这三个HTML标签))
	 * @return void
	 */
	public static function xssFilter(string &$str, string $allowedHTML = null) : void
	{
		$str = preg_replace(array_merge(self::DEFAULT_XSS_FILTER, static::$customFilter), '', strip_tags($str, $allowedHTML));
	}

	/**
	 * 返回整个的请求数据 (默认返回原型)
	 *
	 * @author HanskiJay
	 * @since  2021-02-06
	 * @param  bool           $useXssFilter 是否使用默认的XSS过滤函数
	 * @param  callable|null  callback      回调参数
	 * @return array (开发者需注意在此返回参数时必须使回调参数返回数组)
	 */
	public static function getRequestMerge(bool $useXssFilter = true, ?callable $callback = null) : array
	{
		if($useXssFilter) {
			$get = $post = [];
			foreach(get(owohttp) as $k => $v) {
				$k = trim($k);
				$v = trim($v);
				static::xssFilter($k);
				static::xssFilter($v);
				$get[$k] = $v;
			}
			foreach(post(owohttp) as $k => $v) {
				$k = trim($k);
				$v = trim($v);
				static::xssFilter($k);
				static::xssFilter($v);
				$post[$k] = $v;
			}
			$array = ['get' => $get, 'post' => $post];
		} else {
			$array = ['get' => get(owohttp), 'post' => post(owohttp)];
		}
		return !is_null($callback) ? call_user_func_array($callback, $array) : $array;
	}


    /**
	 * ~ URI/URL 方法
	 */
	/**
	 * 判断是否为HTTPS协议
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return boolean
	 */
	public static function isSecure() : bool
	{
		return (!empty(server('HTTPS')) && 'off' != strtolower(server('HTTPS')))
			|| (!empty(server('SERVER_PORT')) && 443 == server('SERVER_PORT'));
	}

	/**
	 * 获取完整请求HTTP地址
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return string
	*/
	public static function getCompleteUrl() : string
	{
		return server('REQUEST_SCHEME') . '://' . server('HTTP_HOST') . server('REQUEST_URI');
	}

	/**
	 * 获取根地址
	 *
	 * @author HanskiJay
	 * @since  2020-09-09 18:03
	 * @return string
	 */
	public static function getRootUrl() : string
	{
		return server('REQUEST_SCHEME') . '://' . server('HTTP_HOST');
	}

	/**
	 * 返回自定义Url
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @param  string      $name 名称
	 * @param  string      $path 路径
	 * @return string
	 */
	public static function betterUrl(string $name, string $path) : string
	{
		return trim($path, '/') . '/' . str_replace('//', '/', ltrim(((strpos($name, './') === 0) ? substr($name, 2) : $name), '/'));
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
			self::setPathInfo(str_replace(server('SCRIPT_NAME'), '', server('REQUEST_URI')));
			// ↓ Double check & set to '/' when last sentence does not work;
			if(static::$_pathInfo === '') self::setPathInfo();
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
		$param = array_filter(explode('/', self::getPathInfo()));
		if(($getFrom >= 1) && ($getFrom <= 3)) {
			return array_slice($param, $getFrom);
		} else {
			return $param;
		}
	}


	/**
	 * ~ 其他操作方法
	 */
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
		$app = MasterManager::getInstance()->getUnit('app');
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
				return $app->getApp($closure->appName);

			case 'controller':
				return $app->getApp($closure->appName)->getController($closure->controllerName);

			case 'param':
			case 'params':
			case 'parameter':
			case 'parameters':
			case 'args':
			case 'arguments':
				return $closure->urlParameters;

			case 'response':
				return $closure->response;
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
