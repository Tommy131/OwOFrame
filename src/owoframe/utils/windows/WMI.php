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
 * @Date         : 2023-02-09 22:41:43
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 22:45:23
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 *
 * @link https://www.sitepoint.com/php-wmi-dig-deep-windows-php/  (original)
 * @link https://blog.csdn.net/culh2177/article/details/108385131 (zh-CN translated)
 * @link https://docs.microsoft.com/en-us/windows/win32/api/wbemcli/nn-wbemcli-iwbemservices (MS API DOCS)
 * @link https://docs.microsoft.com/en-us/windows/win32/cimwin32prov/computer-system-hardware-classes (MS API DOCS)
 */
declare(strict_types=1);
namespace owoframe\utils\windows;



use COM;
use Variant;
use Throwable;

use owoframe\exception\ExtensionNotFoundException;

class WMI
{
    /**
     * 连接到的命名空间
     *
     * @access protected
     * @var string
     */
    protected $namespace = 'root\cimv2';

    /**
     * 执行脚本
     *
     * @access protected
     * @var string
     */
    protected $script = 'WbemScripting.SWbemLocator';

    /**
     * COM对象
     *
     * @access protected
     * @var COM
     */
    protected $COM;

    /**
     * WMI关键配置文件
     *
     * @access protected
     * @var array
     */
    protected $config =
    [
        'host' => '127.0.0.1',
        'user' => '',
        'pass' => ''
    ];

    /**
     * 连接实例
     *
     * @access protected
     * @var Variant
     */
    protected $connection;


    public function __construct(?string $script = null)
    {
        if(!extension_loaded('com_dotnet')) {
            throw new ExtensionNotFoundException('com_dotnet');
        }
        $this->script = $script ?? $this->script;
        $this->COM    = new COM($this->script);
    }

    /**
     * 设置/更新WMI关键配置文件
     *
     * @param  string $index
     * @param  string $value
     * @return void
     */
    public function set(string $index, string $value) : void
    {
        $this->config[$index] = $value;
    }

    /**
     * 获取WMI关键配置文件
     *
     * @param  string $index
     * @param  mixed  $default
     * @return mixed
     */
    public function config(string $index, $default = null)
    {
        return $this->config[$index] ?? $default;
    }

    /**
     * 返回或创建一个COM连接
     *
     * @return Variant
     */
    public function getConnection() : Variant
    {
        if($this->connection instanceof Variant) {
            return $this->connection;
        }

        try {
            $this->connection = $this->COM->ConnectServer($this->config('host'), $this->namespace, $this->config('user', exec('whoami')), $this->config('pass'));
        } catch(Throwable $e) {
            if($e->getCode() === -2147352567) {
                $this->connection = $this->COM->ConnectServer($this->config('host'), $this->namespace, null, null);
            }
        }

        if($this->connection) {
            $this->connection->Security_->impersonationLevel = $this->config('level', 3);
        }
        return $this->connection;
    }

    /**
     * 返回MI实例
     *
     * @link   https://powershell.one/wmi/root/cimv2 (所有可获取的类名)
     * @param  string  $className
     * @return Variant
     */
    public function getWMI(string $className) : Variant
    {
        return $this->getConnection()->ExecQuery('SELECT * FROM ' . $className);
    }

    /**
     * 返回COM实例化对象
     *
     * @return COM
     */
    public function COM() : COM
    {
        return $this->COM;
    }
}