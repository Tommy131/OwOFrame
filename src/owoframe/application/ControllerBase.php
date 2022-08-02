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

use owoframe\helper\Helper;
use owoframe\http\HttpManager;

abstract class ControllerBase
{
	/**
	 * 返回AppBase实例
	 *
	 * @access private
	 * @var AppBase
	 */
	private $app;

	/**
	 * 若请求的Url中包含无效的请求方法, 则默认执行该方法
	 *
	 * @var string
	 */
	public static $autoInvoke_methodNotFound = 'methodNotFound';

	/**
	 * 显示加载时间DIV的开关
	 *
	 * @var boolean
	 */
	public static $showUsedTimeDiv = true;



	public function __construct(AppBase $app)
	{
		$this->app = $app;
	}

	/**
	 * 这只是一个示例, 参考上方注释
	 *
	 * @author HanskiJay
	 * @since  2020-10-08 22:04
	 * @return mixed
	 */
	public function methodNotFound()
	{
		return '[MethodMiss] Requested method \'' .HttpManager::getCurrent('controllerName') . '::' . HttpManager::getCurrent('methodName') . '\' not found!';
	}

	/**
	 * 开启或关闭 UsedTimeDiv (Default:true)
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  boolean      $_ 状态
	 * @return string
	 */
	public static function showUsedTimeDiv(bool $_ = true) : void
	{
		static::$showUsedTimeDiv = $_;
	}

	/**
	 * 返回控制器类名
	 *
	 * @author HanskiJay
	 * @since  2021-02-09
	 * @return string
	 */
	final public function getName() : string
	{
		return Helper::getShortClassName($this);
	}

	/**
	 * 返回对应的App
	 *
	 * @author HanskiJay
	 * @since  2020-09-10 18:49
	 * @return AppBase
	 */
	final public function getApp() : AppBase
	{
		return $this->app;
	}
}
?>