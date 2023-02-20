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
 * @Date         : 2023-02-09 16:57:27
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-19 04:18:06
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\application;

use owoframe\exception\InvalidControllerException;

abstract class Application
{
    /**
     * 应用程序配置文件
     *
     * @var array
     */
    protected static $config =
    [
        # 应用程序名称ID
        'name'        => 'default',
        # 基本信息
        'author'      => 'OwOTeam',
        'version'     => '1.0.0',
        'description' => 'default description',
        # 允许应用程序在PHP模式下加载 (CGI, CLI)
        'loadMode'    => ['cgi', 'cli']
    ];

    /**
     * 默认控制器
     *
     * @var string|null
     */

    protected $defaultController = null;
    /**
     * 禁止访问的控制器
     *
     * @var array
     */

    protected $controllerBanList = [];
    /**
     * 自动返回404页面
     *
     * @var boolean
     */

    protected $autoTo404Page = true;


    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 检测控制器是否被封禁
     *
     * @param  string  $name
     * @param  boolean $strict
     * @return boolean
     */
    public function isControllerBanned(string $name, bool $strict = false) : bool
    {
        $_ = isset($this->controllerBanList[$name]);
        $_ = $strict ? (count($this->controllerBanList[$name]) > 0) && $_ : $_;
        return $_;
    }

    /**
     * 封禁控制器 (禁止访问)
     *
     * @param  string      $name
     * @return Application
     */
    public function banController(string $name) : Application
    {
        if(!$this->isControllerBanned($name)) {
            $this->controllerBanList[$name] = [];
        }
        return $this;
    }

    /**
     * 解除封禁控制器
     *
     * @param  string      $name
     * @param  boolean     $force
     * @return Application
     */
    public function unbanController(string $name, bool $force = false) : Application
    {
        if($this->isControllerBanned($name)) {
            $_ =& $this->controllerBanList[$name];
            if($force && (count($_) === 0)) {
                unset($_);
            }
        }
        return $this;
    }

    /**
     * 检测控制器方法是否被封禁
     *
     * @param  string  $name
     * @param  string  $method
     * @return boolean
     */
    public function isControllerMethodBanned(string $name, string $method) :bool
    {
        if(!$this->isControllerBanned($name)) {
            return false;
        }
        return in_array($method, $this->controllerBanList[$name]);
    }

    /**
     * 封禁控制器方法
     *
     * @param  string      $name
     * @param  string      $method
     * @return Application
     */
    public function banControllerMethod(string $name, string $method) : Application
    {
        if(!$this->isControllerMethodBanned($name, $method)) {
            $this->controllerBanList[$name][] = $method;
        }
        return $this;
    }

    /**
     * 解除封禁控制器方法
     *
     * @param  string      $name
     * @param  string      $method
     * @return Application
     */
    public function unbanControllerMethod(string $name, string $method) : Application
    {
        if($this->isControllerMethodBanned($name, $method)) {
            $_ =& $this->controllerBanList[$name];
            unset($_[array_search($method, $_)]);
        }
        return $this;
    }

    /**
     * 判断是否存在一个控制器
     *
     * @param  string  $name
     * @param  string  $controller
     * @return boolean
     */
    public function hasController(string $name, ?string &$controller = null) : bool
    {
        $controller = \owo\class_parse_namespace(static::class) . '\\controller\\' . ucfirst(strtolower($name));
        return class_exists($controller) && is_a($controller, Controller::class, true);
    }

    /**
     * 返回控制器对象
     *
     * @param  string  $name
     * @return Controller|null
     */
    public function getController(string $name) : ?Controller
    {
        return $this->hasController($name, $controller) ? (new $controller($this)) : null;
    }

    /**
     * 设置默认控制器
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultController(string $name) : void
    {
        if(!$this->getController($name)) {
            throw new InvalidControllerException("Cannot find Controller '{$name}' on " . static::getName());
        }
        $this->defaultController = $name;
    }

    /**
     * 获取默认控制器
     *
     * @param  bool $returnName
     * @return string|boolean|Controller
     */
    public function getDefaultController(bool $returnName = false)
    {
        if(!$this->defaultController) {
            return false;
        }
        return $returnName ? $this->defaultController : $this->getController($this->defaultController);
    }



    #-------------------------------------------------------------#
    #                          基本方法                           #
    #-------------------------------------------------------------#
    /**
     * 返回应用程序路径
     *
     * @return string
     */
    public static function getPath() : string
    {
        return \owo\application_path(self::getName(), true);
    }

    /**
     * 应用程序配置文件
     *
     * @param  string $index
     * @return mixed
     */
    public static function config(string $index)
    {
        return static::$config[$index] ?? null;
    }

    public static function getName() : string
    {
        return static::config('name');
    }

    /**
     * 应用程序作者
     *
     * @return string
     */
    public static function getAuthor() : string
    {
        return static::config('author');
    }

    /**
     * 应用程序介绍
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return static::config('author');
    }

    /**
     * 应用程序版本
     *
     * @return string
     */
    public static function getVersion() : string
    {
        return static::config('version');
    }

    /**
     * 魔术方法
     *
     * @param  string $name
     * @param  string $value
     * @return void
     */
    public function __set(string $name, $value)
    {
        static::$config[$name] = $value;
    }

    /**
     * 魔术方法
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return static::config($name);
    }

    /**
     * 初始化应用程序时自动调用该方法
     *
     * @return void
     */
    abstract public function initialize() : void;
}
?>