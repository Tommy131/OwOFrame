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
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

declare(strict_types=1);
namespace backend\system\app;

use backend\system\module\DataEncoder;
use backend\system\route\Router;
use backend\system\route\RouteRule;
use backend\system\exception\OwOFrameException;
use backend\system\exception\InvalidControllerException;
use backend\system\exception\ParameterErrorException;

use backend\system\db\DbConfig;

abstract class AppBase
{
	/* @string App名称 */
	private $appName = null;
	/* @string 默认控制其名称 */
	private $defaultController = "";
	/* @string 当前的App访问地址 */
	private $siteUrl = null;
	/* @Array 从请求Url传入的原始get参数 */
	protected $parameter = [];
	/* @AppBase 返回本类实例 */
	private static $instance = null;


	public function __construct(string $appName, string $siteUrl, array $parameter = [])
	{
		if(($appName === '') || ($appName === null)) {
			throw new ParameterErrorException('appName', "String", get_class($this));
		}
		if(self::$instance === null) {
			self::$instance = $this;
		} else {
			throw new OwOFrameException("App '{$appName}' has been initialized!");
		}
		$this->appName   = $appName;
		$this->siteUrl   = $siteUrl;
		$this->parameter = $parameter;
		$this->initialize();
	}

	/**
	 * @method      setParameters
	 * @description 外部调用, 设置从Url获取到当前的GET请求参数
	 * @description External call, set the GET request parameters obtained from current Url
	 * @param       array[parameter|需要设置的参数数组]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function setParameters(array $parameter) : void
	{
		$this->parameter = $parameter;
	}

	/**
	 * @method      setDefaultController
	 * @description 设置默认控制器
	 * @param       string[defaultController|默认控制器名称]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function setDefaultController(string $defaultController) : void
	{
		if($this->getController($defaultController) === null) {
			throw new InvalidControllerException($this->getName(), $defaultController, get_class($this));
		}
		$this->defaultController = $defaultController;
	}

	/**
	 * @method      getDefaultController
	 * @description 获取默认控制器
	 * @param       bool[returnName|返回控制器名称](Default:false)
	 * @return      string or ControllerBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function getDefaultController(bool $returnName = false)
	{
		return $returnName ? $this->defaultController : $this->getController($this->defaultController);
	}

	/**
	 * @method      getController
	 * @description 获取一个有效的控制器
	 * @description Return a valid Controller
	 * @param       string[controllerName|控制器名称]
	 * @return      null or ControllerBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function getController(string $controllerName) : ?ControllerBase
	{
		$controller = "\\backend\\application\\{$this->getName()}\\controller\\" . $controllerName;
		return class_exists($controller) ? (new $controller($this)) : null;
	}

	/**
	 * @method      getInstance
	 * @description 返回本类实例
	 * @description Return this class object
	 * @return      AppBase
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public static function getInstance() : AppBase
	{
		return self::$instance;
	}

	/**
	 * @method      renderPageNotFound
	 * @description 渲染404页面(未匹配有效的控制器时)
	 * @description Render 404 page (when no valid controller is matched)
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function renderPageNotFound() : void
	{
		$sendMessage = '404 PAGE NOT FOUND';
		if(\OwOBootstrap\useJsonFormat()) {
			DataEncoder::reset();
			die(DataEncoder::setStandardData(404, false, $sendMessage));
		} else {
			die($sendMessage);
		}
	}

	/**
	 * @method      getAppPath
	 * @description 获取当前App目录
	 * @param       bool[selectMode|选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function getAppPath(bool $selectMode = true) : string
	{
		return (($selectMode) ? AppManager::getPath() : $this->getNameSpace()) . $this->getName() . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getName
	 * @description 获取App名称
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function getName() : string
	{
		return $this->appName;
	}

	/**
	 * @method      getNameSpace
	 * @description 自动解析并返回当前App的命名空间;
	 * @description Automatically parse and return the namespace of the current App
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	final public function getNameSpace() : string
	{
		$ns = explode("\\", get_class($this));
		return implode("\\", array_slice($ns, 0, count($ns) - 1));
	}



	/* 抽象化方法 | Abstraction Methods */

	/**
	 * @method      initialize
	 * @description 初始化App时自动调用该方法;
	 * @description A Method for when the Application in initialzation;
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	abstract public function initialize() : void;

	/**
	 * @method      autoTo404Page
	 * @description 告知路由组件是否自动跳转到404页面(如果指定)
	 * @description Tell the Router whether to automatically jump to the 404 page (if specified)
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	abstract public static function autoTo404Page() : bool;
}
?>