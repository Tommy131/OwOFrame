<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-09 17:14:43
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 17:27:06
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\application;

use owoframe\http\Response;
use owoframe\utils\DataEncoder;

abstract class Controller
{
	/**
     * 返回Application实例
     *
     * @access private
     * @var Application
     */
    private $app;

    /**
     * 响应对象
     *
     * @var Response
     */
    protected $response;


	/**
	 * 构造函数
	 *
	 * @param Application|null $app
	 */
    public function __construct(?Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * 设置响应对象
     *
     * @param  Response $response
     * @return void
     */
    public function setResponse(Response $response) : void
    {
        $this->response = $response;
    }

    /**
     * 返回响应对象
     *
     * @return Response|null
     */
    public function getResponse() : ?Response
    {
        return $this->response ?? null;
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
	 * 发送错误响应载体
	 *
	 * @param  integer     $responseCode
	 * @return DataEncoder
	 */
    public static function responseErrorStatus(int $responseCode = 502) : DataEncoder
	{
		$args = func_get_args();
	    return (new DataEncoder)->setStandardData($responseCode, $args[1] ?? 'Unknown error happened from Server', false)->setIndex('ip', \owo\server('REMOTE_ADDR'));
	}

    /**
     * 返回控制器类名
     *
     * @return string
     */
    final public function getName() : string
    {
        return \owo\class_short_name($this);
    }

    /**
     * 返回对应的App
     *
     * @return Application|null
     */
    final public function getApp() : ?Application
    {
        return $this->app ?? null;
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
	    return self::responseErrorStatus(502, 'Attempt to request undefined method ' . __CLASS__ . '::' . $name);
	}
}
?>
