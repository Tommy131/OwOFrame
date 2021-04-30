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
namespace owoframe\application;

use owoframe\exception\OwOFrameException;
use owoframe\exception\InvalidControllerException;
use owoframe\http\HttpManager;
use owoframe\helper\Helper;
use owoframe\module\{ModuleBase, ModuleLoader};
use owoframe\utils\DataEncoder;

abstract class AppBase
{
	/* @AppBase 返回本类实例 */
	private static $instance = null;
	/* @string 当前的App访问地址 */
	private $siteUrl = null;
	/* @string 默认控制其名称 */
	protected $defaultController = '';
	/* @Array 从请求Url传入的原始get参数 */
	protected $parameter = [];
	/* @array 不允许通过路由请求的控制器(方法)组 */
	protected $contronllerFilter = [];


	public function __construct(string $siteUrl, array $parameter = [])
	{
		if(static::$instance === null) {
			static::$instance = $this;
		} else {
			throw new OwOFrameException("App '{$appName}' has been initialized!");
		}
		$this->siteUrl   = $siteUrl;
		$this->parameter = $parameter;
		$this->initialize();
	}

	/**
	 * @method      renderPageNotFound
	 * @description 渲染404页面(未匹配有效的控制器时)
	 * @description Render 404 page (when no valid controller is matched)
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      string
	*/
	public static function renderPageNotFound() : string
	{
		HttpManager::setStatusCode(404);
		$sendMessage = (isset(static::$renderPageNotFound) && is_string(static::$renderPageNotFound)) ? static::$renderPageNotFound : '404 PAGE NOT FOUND';
		if(isset(static::$renderWithJSON)) {
			$render = new DataEncoder;
			$render->setStandardData(404, $sendMessage, false);
			return $render->encode();
		} else {
			return $sendMessage;
		}
	}

	/**
	 * @method      setParameters
	 * @description 外部调用, 设置从Url获取到当前的GET请求参数
	 * @description External call, set the GET request parameters obtained from current Url
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       array      $parameter 需要设置的参数数组
	 * @return      void
	*/
	public function setParameters(array $parameter) : void
	{
		$this->parameter = $parameter;
	}

	/**
	 * @method      isControllerMethodBanned
	 * @description 判断控制器的方法是否不允许直接访问
	 * @author      HanskiJay
	 * @doenIn      2021-04-30
	 * @param       string                   $methodName     方法名
	 * @param       string                   $controllerName 控制器名
	 * @return      boolean
	 */
	public function isControllerMethodBanned(string $methodName, string $controllerName) : bool
	{
		$controllerName =ucfirst(strtolower($controllerName));
		return isset($this->contronllerFilter[$controllerName]) && in_array($methodName, $this->contronllerFilter[$controllerName]);
	}

	/**
	 * @method      banControllerMethod
	 * @description 禁止通过路由请求此控制器的方法
	 * @author      HanskiJay
	 * @doenIn      2021-04-29
	 * @param       string              $controllerName 控制器名
	 * @param       array               $args           多选方法名组
	 * @return      void
	 */
	public function banControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName = ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		if(!isset($this->contronllerFilter[$controllerName])) {
			$this->contronllerFilter[$controllerName] = [];
		}
		$this->contronllerFilter[$controllerName] = array_merge($this->contronllerFilter[$controllerName], $args);
	}

	/**
	 * @method      allowControllerMethod
	 * @description 允许通过路由请求此控制器的方法
	 * @author      HanskiJay
	 * @doenIn      2021-04-29
	 * @param       string              $controllerName 控制器名
	 * @param       array               $args           多选方法名组
	 * @return      void
	 */
	public function allowControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName =ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		foreach($args as $key => $methodName) {
			if($this->isControllerMethodBanned($controllerName, $methodName)) {
				unset($this->contronllerFilter[$controllerName][$key]);
			}
		}
		ksort($this->contronllerFilter);
	}

	/**
	 * @method      setDefaultController
	 * @description 设置默认控制器
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $defaultController 默认控制器名称
	 * @return      void
	*/
	public function setDefaultController(string $defaultController) : void
	{
		if(!$this->getController($defaultController)) {
			throw new InvalidControllerException(static::getName(), $defaultController);
		}
		$this->defaultController = $defaultController;
	}

	/**
	 * @method      getDefaultController
	 * @description 获取默认控制器
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       bool      $returnName 返回控制器名称
	 * @return      string|@ControllerBase
	*/
	public function getDefaultController(bool $returnName = false)
	{
		return $returnName ? $this->defaultController : $this->getController($this->defaultController);
	}

	/**
	 * @method      getController
	 * @description 获取一个有效的控制器
	 * @description Return a valid Controller
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $controllerName 控制器名称
	 * @return      boolean|@ControllerBase
	*/
	public function getController(string $controllerName, bool $autoMake = true)
	{
		$controller = '\\application\\' . static::getName() . '\\controller\\' . $controllerName;
		if(class_exists($controller)) {
			return ($autoMake) ? new $controller($this) : true;
		} else {
			return false;
		}
	}

	/**
	 * @method      getCachePath
	 * @description 返回本Application的Cache目录
	 * @author      HanskiJay
	 * @doenIn      2021-03-14
	 * @param       string     $option 可选参数(文件/文件夹路径)
	 * @return      string
	 */
	public static function getCachePath(string $option = '') : string
	{
		return A_CACHE_PATH . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * @method      getAppPath
	 * @description 获取当前App目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       bool      $selectMode 选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return      string
	*/
	final public static function getAppPath(bool $selectMode = true) : string
	{
		return (($selectMode) ? AppManager::getPath() : static::getNameSpace()) . static::getName() . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getNameSpace
	 * @description 自动解析并返回当前App的命名空间
	 * @description Automatically parse and return the namespace of the current App
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      string
	*/
	final public static function getNameSpace() : string
	{
		$ns = explode("\\", __CLASS__);
		return implode("\\", array_slice($ns, 0, count($ns) - 1));
	}

	/**
	 * @method      getModule
	 * @access      protected
	 * @description 获取模块实例化对象
	 * @author      HanskiJay
	 * @doenIn      2021-02-08
	 * @param       string      $name 插件名称
	 * @return      null|@ModuleBase
	 */
	final protected function getModule(string $name) : ?ModuleBase
	{
		return ModuleLoader::getModule($name);
	}

	/**
	 * @method      getInstance
	 * @description 返回本类实例
	 * @description Return this class instance
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      null|@AppBase
	*/
	public static function getInstance() : ?AppBase
	{
		return static::$instance ?? null;
	}



	/* 抽象化方法 | Abstraction Methods */

	/**
	 * @method      initialize
	 * @description 初始化App时自动调用该方法
	 * @description A Method for when the Application in initialzation
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      void
	*/
	abstract public function initialize() : void;

	/**
	 * @method      autoTo404Page
	 * @description 告知路由组件是否自动跳转到404页面(如果指定)
	 * @description Tell the Router whether to automatically jump to the 404 page (if specified)
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      boolean
	*/
	abstract public static function autoTo404Page() : bool;

	/**
	 * @method      getName
	 * @description 获取App名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      string
	*/
	abstract public static function getName() : string;
}
?>