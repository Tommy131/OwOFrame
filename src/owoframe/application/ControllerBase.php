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

use owoframe\System;
use owoframe\http\HttpManager;
use owoframe\utils\DataEncoder;

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
     * 返回默认处理请求的方法
     *
     * @return string|null
     */
    public function getDefaultHandlerMethod() : ?string
    {
        return null;
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
        return System::getShortClassName($this);
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


	/**
	 * 发送错误响应载体
	 *
	 * @param  integer $responseCode
	 * @return DataEncoder
	 */
	protected function responseErrorStatus(int $responseCode = 502) : DataEncoder
	{
		HttpManager::getCurrent('response')->setResponseCode($responseCode);
		$args = func_get_args();
		return (new DataEncoder)->setStandardData($responseCode, $args[1] ?? 'Unknown error happened from Server', false)->setIndex('ip', server('REMOTE_ADDR'));
	}

	/**
	 * 魔术方法: 方法未找到时执行
	 *
	 * @param  string $name
	 * @param  array  $arguments
	 * @return void
	 */
	public function __call(string $name, array $arguments)
	{
		return $this->responseErrorStatus(502, 'Attempt to request undefined method ' . __CLASS__ . '::' . $name);
	}
}
?>