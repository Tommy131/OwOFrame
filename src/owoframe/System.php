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
 * @Date         : 2023-02-02 16:07:57
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-19 04:07:43
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe;

use Throwable;
use FilesystemIterator as FI;

use owoframe\application\Application;
use owoframe\application\standard\DefaultApp;
use owoframe\event\system\OutputEvent;
use owoframe\exception\OwOFrameException;
use owoframe\module\ModuleLoader;
use owoframe\object\INI;
use owoframe\template\View;
use owoframe\utils\Logger;

final class System
{
    /**
     * PHP错误常量转换
     */
    public const ERROR_CONVERSION =
    [
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
    ];

    /**
     * 已加载的应用程序列表
     *
     * @var array
     */
    public static $loadedApplications = [];


    private function __construct()
    {
    }

    /**
     * 初始化函数
     *
     * @return void
     */
    public static function init() : void
    {
        if(defined('SYSTEM_INITIALIZED')) {
            return;
        }
        // Set up exception crawling
        set_error_handler([System::class, 'ErrorHandler'], E_ALL);
        set_exception_handler([System::class, 'ExceptionHandler']);

        // initialize system paths
        \owo\get_class_loader()->addPsr4('application\\', \owo\application_path());
        \owo\get_class_loader()->addPsr4('module\\',      \owo\module_path());
        \owo\create_paths();


        // Generate global configuration file
        self::generateConfig();

        // Define Timezone
        if(!defined('TIME_ZONE')) {
            define('TIME_ZONE', (\owo\_global('owo.timeZone', 'Europe/Berlin')));
        }
        date_default_timezone_set(TIME_ZONE);

        // Use output buffer | 启用输出缓冲区
        if(!ob_get_level() && \owo\_global('system.useOutputBuffer', true) && !\owo\php_is_cli()) {
            ob_start();
        }

        // Load Module(s)
        ModuleLoader::autoLoad(\owo\module_path());

        // 定义系统已经初始化
        define('SYSTEM_INITIALIZED', true);
    }

    /**
     * 创建配置文件
     *
     * @return void
     */
    public static function generateConfig() : void
    {
        $ini = new INI(\owo\config_path('global.ini'), [
            'owo' => [
                'debugMode'  => true,
                'enableLog'  => false,
                'timeZone'   => 'Europe/Berlin',
                'defaultApp' => 'index',
                # 若存在多个禁止访问的Application, 请使用逗号分隔 (不能含有空格)
                # If you need deny more than 1 Application, please use comma to split (do not use space)
                # e.g.|例子: index,test,config
                'denyList'   => null
            ],
            'view' => [
                'loopLevel'      => 3,
                'judgementLevel' => 3
            ],
            'mysql' => [
                'default'  => 'mysql',
                'type'     => 'mysql',
                'username' => 'root',
                'password' => '123456',
                'hostname' => '127.0.0.1',
                'port'     => 3306,
                'charset'  => 'utf8mb4',
                'database' => 'owoblogserver',
                'prefix'   => null
            ],
            'redis' => [
                'enable' => true,
                'server' => '127.0.0.1',
                'port'   => 5300,
                'auth'   => '123456'
            ],
            'system' => [
                'autoInitDatabase' => true
            ]
        ], true);
        INI::toGlobal($ini);
    }

    /**
     * 返回当前框架是否处于调试模式
     *
     * @return boolean
     */
    public static function isDebugMode() : bool
    {
        $debugMode = \owo\_global('owo.debugMode', true);
        return \owo\str2bool($debugMode);
    }



    #-------------------------------------------------------------#
    #                     Application管理方法                     #
    #-------------------------------------------------------------#
    /**
     * 判断是否存在一个应用程序
     *
     * @param  string $appName
     * @param  string $class
     * @return boolean
     */
    public static function hasApplication(string $appName, &$class = null) : bool
    {
        $name    = strtolower($appName);
        $appName = ucfirst($name);
        $class   = "\\application\\{$name}\\{$appName}" . 'App';
        return class_exists($class) && is_a($class, Application::class, true);
    }

    /**
     * 获取指定应用程序
     *
     * @param  string $appName
     * @return Application|null
     */
    public static function getApplication(string $appName) : ?Application
    {
        if(isset(self::$loadedApplications[$appName])) {
            return self::$loadedApplications[$appName];
        }
        if(self::hasApplication($appName, $class) && in_array(\owo\php_current(), $class::config('loadMode'))) {
            return self::$loadedApplications[$appName] = new $class();
        }
        return null;
    }

    /**
     * 获取默认应用程序
     *
     * @param  boolean $allowNull
     * @return Application|null
     */
    public static function getDefaultApplication(bool $allowNull = false) : ?Application
    {
        return self::getApplication(\owo\_global('owo.defaultApp')) ?? ($allowNull ? null : (new DefaultApp));
    }

    /**
     * 初始化所有应用程序
     *
     * @return void
     */
    public static function initializeApplications() : void
    {
        $path = new FI(\owo\application_path(), FI::KEY_AS_FILENAME | FI::SKIP_DOTS);
        foreach($path as $info) {
            if(is_dir($info->getPathName())) {
                $appName = $info->getFileName();
                // 过滤文件名
                if(!preg_match('/[a-z0-9]+/i', $appName)) continue;
                self::getApplication($appName);
            }
        }
    }



    #-------------------------------------------------------------#
    #                       异常输出渲染方法                       #
    #-------------------------------------------------------------#
    /**
     * 返回主日志实例
     *
     * @return Logger
     */
    public static function getMainLogger() : Logger
    {
        static $logger;
        if(!$logger instanceof Logger) {
            $logger = new Logger;
        }
        $logger->fileName  = 'owoblog_run_' . \owo\php_current() . '.log';
        return $logger;
    }

    /**
     * 返回错误日志实例
     *
     * @return Logger
     */
    public static function getErrorLogger() : Logger
    {
        static $logger;
        if(!$logger instanceof Logger) {
            $logger = new Logger;
            $logger->logPrefix = 'OwOError';
        }
        $logger->fileName  = 'owoblog_error_' . \owo\php_current() . '.log';
        return $logger;
    }

    /**
     * 返回渲染完成的模板
     *
     * @param  array $args
     * @return void
     */
    private static function execute(array $args) : void
    {
        if(\owo\php_is_cgi()) {
            $view = new View('ExceptionHandlerTemplate.html', \owo\s_template_path());
            $args = array_map(function($v) {return $v ?? 'NONE';}, $args);
            $view->assign([
                'type'      => $args[0],
                'subtype'   => $args[1],
                'message'   => $args[2],
                'file'      => $args[3],
                'line'      => $args[4],
                'trace'     => $args[5],
                'runTime'   => \owo\runtime(),
                'debugMode' => self::isDebugMode() ? '<span id="debugMode">DebugMode</span>' : ''
            ]);
            $output = new OutputEvent($view->render());
            $output->trigger();
            $output->output();
        }
    }

    /**
     * 更好的 DEBUG 追踪
     *
     * @return string
     */
    private static function betterTrace() : string
    {
        $output   = '';
        $template = "#%d %s(%d): %s%s%s(%s)\n";
        $trace    = debug_backtrace();
        unset($trace[0]);
        $trace = array_values($trace);

        foreach($trace as $k => $d) {
            $args = [];
            foreach($d['args'] ?? [] as $arg) {
                $args[] = (gettype($arg) === 'object') ? (array) $arg : $arg;
            }
            $output .= sprintf($template, $k, $d['file'] ?? '', $d['line'] ?? '', $d['class'] ?? '', $d['type'] ?? '', $d['function'] ?? '', @implode(', ', $args));
        }
        return $output . "{main}\n";
    }

    /**
     * 错误处理方法
     *
     * @return void
     */
    public static function ErrorHandler(int $level, string $message, string $file, int $line) : void
    {
        if(error_reporting() === 0) return;
        $level = self::ERROR_CONVERSION[$level] ?? $level;
        $trace = self::betterTrace();
        self::execute(['PHP', $level, $message, $file, $line, $trace]);
        self::getErrorLogger()->emergency("{$level} happened: {$message} in {$file} at line {$line}\nStack trace:\n{$trace}");
        exit(500);
    }

    /**
     * 异常处理方法
     *
     * @param  Throwable $exception
     * @return void
     */
    public static function ExceptionHandler(Throwable $exception) : void
    {
        $type = 'PHP';
        if($exception instanceof OwOFrameException) {
            $fileName = $exception->getRealFile();
            $realName = $exception->getRealLine();
            $type = 'OwO';
        } else {
            $fileName = $exception->getFile();
            $realName = $exception->getLine();
        }
        self::execute([$type, \owo\class_short_name($exception), $exception->getMessage(), $fileName, $realName, $exception->getTraceAsString()]);
        self::getErrorLogger()->emergency($exception);
        exit(500);
    }
}
?>