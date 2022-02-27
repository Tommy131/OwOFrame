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

use owoframe\exception\InvalidControllerException;
use owoframe\module\ModuleBase;
use owoframe\module\ModuleLoader;

abstract class AppBase
{
	/**
	 * 返回本类实例
	 *
	 * @access protected
	 * @var AppBase
	 */
	protected static $instance = null;

	/**
	 * 当前的App访问地址
	 *
	 * @access protected
	 * @var string
	 */
	protected $currentSiteUrl = null;

	/**
	 * 默认控制其名称
	 *
	 * @access protected
	 * @var string
	 */
	protected $defaultController = '';

	/**
	 * 不允许通过路由请求的控制器(方法)组
	 *
	 * @access protected
	 * @var array
	 */
	protected $controllerFilter = [];



	public function __construct(string $siteUrl)
	{
		if(static::$instance === null) {
			static::$instance = $this;
		}
		$this->currentSiteUrl = $siteUrl;
		$this->initialize();
	}

	/**
	 * 判断控制器是否在过滤组中
	 *
	 * @author HanskiJay
	 * @since  2021-12-28
	 * @param  string  $controllerName
	 * @return boolean
	 */
	public function isControllerInFilter(string $controllerName) : bool
	{
		$controllerName =ucfirst(strtolower($controllerName));
		return isset($this->controllerFilter[$controllerName]);
	}

	/**
	 * 直接禁止整个控制器通过URL访问
	 *
	 * @author HanskiJay
	 * @since  2021-12-28
	 * @param  string $controllerName
	 * @return void
	 */
	public function banController(string $controllerName) : void
	{
		$controllerName =ucfirst(strtolower($controllerName));
		$this->controllerFilter[$controllerName] = 'all';
	}

	/**
	 * 判断指定的控制器是否被封禁
	 *
	 * @author HanskiJay
	 * @since  2021-12-28
	 * @param  string  $controllerName
	 * @return boolean
	 */
	public function isControllerBanned(string $controllerName) : bool
	{
		$controllerName =ucfirst(strtolower($controllerName));
		return $this->isControllerInFilter($controllerName) && ($this->controllerFilter[$controllerName] === 'all');
	}

	/**
	 * 判断控制器的方法是否不允许直接访问
	 *
	 * @author HanskiJay
	 * @since  2021-04-30
	 * @param  string      $controllerName 控制器名
	 * @param  string      $methodName     方法名
	 * @return boolean
	 */
	public function isControllerMethodBanned(string $controllerName, string $methodName) : bool
	{
		$controllerName =ucfirst(strtolower($controllerName));
		return $this->isControllerInFilter($controllerName) && in_array($methodName, $this->controllerFilter[$controllerName]);
	}

	/**
	 * 禁止通过路由请求此控制器的方法
	 *
	 * @author HanskiJay
	 * @since  2021-04-29
	 * @param  string      $controllerName 控制器名
	 * @param  array       $args           多选方法名组
	 * @return void
	 */
	public function banControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName = ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName, false)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		if(!$this->isControllerInFilter($controllerName)) {
			$this->controllerFilter[$controllerName] = [];
		}
		$this->controllerFilter[$controllerName] = array_merge($this->controllerFilter[$controllerName], $args);
	}

	/**
	 * 允许通过路由请求此控制器的方法
	 *
	 * @author HanskiJay
	 * @since  2021-04-29
	 * @param  string      $controllerName 控制器名
	 * @param  array       $args           多选方法名组
	 * @return void
	 */
	public function allowControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName = ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName, false)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		foreach($args as $key => $methodName) {
			if($this->isControllerMethodBanned($controllerName, $methodName)) {
				unset($this->controllerFilter[$controllerName][$key]);
			}
		}
		ksort($this->controllerFilter);
	}

	/**
	 * 设置默认控制器
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  string      $defaultController 默认控制器名称
	 * @return void
	 */
	public function setDefaultController(string $defaultController) : void
	{
		if(!$this->getController($defaultController, false)) {
			throw new InvalidControllerException(static::getName(), $defaultController);
		}
		$this->defaultController = $defaultController;
	}

	/**
	 * 获取默认控制器
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  bool      $returnName 返回控制器名称
	 * @return string|ControllerBase
	 */
	public function getDefaultController(bool $returnName = false)
	{
		return $returnName ? $this->defaultController : $this->getController($this->defaultController);
	}

	/**
	 * 获取一个有效的控制器
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  string      $controllerName 控制器名称
	 * @return mixed|boolean|ControllerBase
	 */
	public function getController(string $controllerName, bool $autoMake = true)
	{
		$controller = '\\application\\' . static::getName() . '\\controller\\' . $controllerName;
		if(class_exists($controller) && is_a($controller, ControllerBase::class, true)) {
			return ($autoMake) ? ($controller = new $controller($this)) : true;
		} else {
			return false;
		}
	}

	/**
	 * 返回本Application的Cache目录
	 *
	 * @author HanskiJay
	 * @since  2021-03-14
	 * @param  string     $option 可选参数(文件/文件夹路径)
	 * @return string
	 */
	public static function getCachePath(string $option = '') : string
	{
		return A_CACHE_PATH . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * 返回本Application的资源目录
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string     $option 可选参数(文件/文件夹路径)
	 * @return string
	 */
	public static function getResourcePath(string $option = '') : string
	{
		return RESOURCE_PATH . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * 返回本Application的存储目录
	 *
	 * @author HanskiJay
	 * @since  2021-12-24
	 * @param  string     $option 可选参数(文件/文件夹路径)
	 * @return string
	 */
	public static function getStoragePath(string $option = '') : string
	{
		return STORAGE_PATH . 'application' . DIRECTORY_SEPARATOR . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * 获取当前App目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @param  bool      $selectMode 选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return string
	 */
	final public static function getAppPath(bool $selectMode = true) : string
	{
		return (($selectMode) ? AppManager::getPath() : static::getNameSpace()) . static::getName() . DIRECTORY_SEPARATOR;
	}

	/**
	 * 自动解析并返回当前App的命名空间
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @return string
	 */
	final public static function getNameSpace() : string
	{
		$ns = explode("\\", __CLASS__);
		return implode("\\", array_slice($ns, 0, count($ns) - 1));
	}

	/**
	 * 获取模块实例化对象
	 *
	 * @author HanskiJay
	 * @since  2021-02-08
	 * @param  string      $name 插件名称
	 * @return ModuleBase|null
	 * @access protected
	 */
	final protected function getModule(string $name) : ?ModuleBase
	{
		return ModuleLoader::getModule($name);
	}

	/**
	 * 返回当前站点Url
	 *
	 * @author HanskiJay
	 * @since  2020-08-08
	 * @return string
	 */
	public function getCurrentSiteUrl() : string
	{
		return $this->currentSiteUrl;
	}

	/**
	 * 返回本类实例
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @return AppBase|null
	 */
	public static function getInstance() : ?AppBase
	{
		return static::$instance ?? null;
	}



	/* 抽象化方法 | Abstraction Methods */

	/**
	 * 初始化App时自动调用该方法
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @return void
	 */
	abstract public function initialize() : void;

	/**
	 * 判断此Application是否仅允许在CLI模式下加载
	 *
	 * @author HanskiJay
	 * @since  2022-02-27
	 * @return boolean
	 */
	abstract public static function isCLIOnly() : bool;

	/**
	 * 告知路由组件是否自动跳转到404页面(如果指定)
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @return boolean
	 */
	abstract public static function autoTo404Page() : bool;

	/**
	 * 获取App名称
	 *
	 * @author HanskiJay
	 * @since  2020-09-09
	 * @return string
	 */
	abstract public static function getName() : string;

	/**
	 * 获取此Application的作者
	 *
	 * @author HanskiJay
	 * @since  2022-02-27
	 * @return string
	 */
	public static function getAuthor() : string
	{
		return '';
	}

	/**
	 * 获取此Application的版本
	 *
	 * @author HanskiJay
	 * @since  2022-02-27
	 * @return string
	 */
	public static function getVersion() : string
	{
		return '1.0.0';
	}

	/**
	 * 获取此Application的描述内容
	 *
	 * @author HanskiJay
	 * @since  2022-02-27
	 * @return string
	 */
	public static function getDescription() : string
	{
		return '';
	}
}
?>