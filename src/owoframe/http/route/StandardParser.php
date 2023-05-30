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
 * @Date         : 2023-02-17 23:02:00
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-25 16:05:31
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http\route;



use owoframe\http\Response;
use owoframe\System;

trait StandardParser
{
    /**
     * 自动发送响应数据
     *
     * @var boolean
     */
    protected $autoSend = true;

    /**
     * 解析关键字
     *
     * @var string|null
     */
    protected $appName;

    /**
     * 解析关键字
     *
     * @var string|null
     */
    protected $controllerName;

    /**
     * 解析关键字
     *
     * @var string|null
     */
    protected $methodName;

    /**
     * 剩余路径
     *
     * @var array
     */
    protected $restPath = [];

    /**
     * 路由別名
     *
     * @var array
     */
    protected $alias = [];


    /**
     * 获取路径别名
     *
     * @return array
     */
    public function getAliases() : array
    {
        return $this->alias;
    }

    /**
     * 设置路径别名
     *
     * @param  string $alias
     * @param  string $name
     * @return Route
     */
    public function setAlias(string $alias, string $name) : Route
    {
        $this->alias[$alias] = $name;
        return $this;
    }

    /**
     * 获取路径别名
     *
     * @param  string      $alias
     * @return string|null
     */
    public function getAlias(string $alias) : ?string
    {
        return $this->alias[$alias] ?? null;
    }

    /**
     * 移除一个路径别名
     *
     * @param  string $alias
     * @return Route
     */
    public function removeAlias(string $alias) : Route
    {
        if($this->getAlias($alias)) {
            unset($this->alias[$alias]);
        }
        return $this;
    }

    /**
     * 合并别名组
     *
     * @param  array $aliases
     * @return Route
     */
    public function mergeAlias(array $aliases) : Route
    {
        $this->alias = array_merge($this->alias, $aliases);
        return $this;
    }

    /**
     * 解析路径
     *
     * @param  integer|null $case
     * @return void
     */
    public function parse(?int $case = null) : void
    {
        $this->restPath = \owo\get_path();
        $length         = count($this->restPath);

        switch($case ?? $length) {
            case 0:
            case 1:
                $this->appName = $this->controllerName = $this->methodName = array_shift($this->restPath) ?? 'unknown';
            break;

            case 2:
                $this->appName        = array_shift($this->restPath);
                $this->controllerName = $this->methodName = array_shift($this->restPath);
            break;

            case 3:
                [$this->appName, $this->controllerName, $this->methodName] = $this->restPath;
            break;

            default:
                if($length > 3) {
                    $this->parse(3);
                }
            break;
        }

        // ~ 第一次检测: 当不存在剩余路径时, 根据已解析的数据再解析
        if(empty($this->restPath) && ($length > 1)) {
            $_ = [$this->methodName, $this->controllerName, $this->appName];
            $_ = array_map(function($v) use (&$_) {
                if(is_numeric($v)) {
                    $this->restPath[] = $v;
                    return !is_null(key($_)) ? next($_) : $v;
                }
                return $v;
            }, $_);
            $this->restPath = array_reverse($this->restPath);
            [$this->methodName, $this->controllerName, $this->appName] = $_;
        }
    }

    /**
     * 检查应用程序基本信息是否有效
     *
     * @return boolean
     */
    public function check(&$controller = null, &$method = null) : bool
    {
        $this->parse();
        $this->appName        = $this->getAlias($this->appName) ?? $this->appName;
        $this->controllerName = $this->getAlias($this->controllerName) ?? $this->controllerName;
        $this->methodName     = $this->getAlias($this->methodName) ?? $this->methodName;

        $app = System::getApplication($this->appName) ?? System::getDefaultApplication();
        if(!$app) {
            return false;
        }
        // 获取控制器
        $controller = $app->getController($this->controllerName);
        $controller = !$controller ? $app->getDefaultController() : $controller;

        // 检查控制器有效性
        if(!$controller || $app->isControllerBanned($controller->getName())) {
            return false;
        }

        // ~ 第二次检测: 当请求的方法不存在于控制器中
        if(empty($this->restPath) && !method_exists($controller, $this->methodName)) {
            $this->restPath[] = $this->methodName;
            $this->methodName = $this->controllerName;
        }
        // ~ 第三次检测: 当请求的控制器不存在于请求的应用程序中
        if(empty($this->restPath)) {
            if($app && !$app->hasController($this->controllerName)) {
                $this->restPath[]     = $this->controllerName;
                $this->controllerName = $this->appName;
            }
        }
        // 检查方法是否存在
        foreach([$this->methodName, $controller->getName(), $controller->getDefaultHandlerMethod()] as $_) {
            if(method_exists($controller, $_)) {
                $method = $_;
                break;
            }
        }
        // 检查方法有效性
        if(!isset($method) || $app->isControllerMethodBanned($controller->getName(), $method)) {
            return false;
        }
        // #[END] 结束检查
        return true;
    }

    /**
     * 起飞
     *
     * @param  array|string $prepareSendData
     * @return boolean
     */
    public function run($prepareSendData = '', int $code = 502) : bool
    {
        $_ = $this->check($controller, $method);
        if($_) {
            $this->response = new Response([$controller, $method], $this->restPath);
            $controller->setResponse($this->response);
            $code = 200;
        } else {
            $this->response = new Response;
            $this->response->setPrepareSendData($prepareSendData);
        }
        $this->response->setResponseCode($code);
        if($this->autoSend) {
            $this->response->send();
        }
        return $_;
    }
}
?>