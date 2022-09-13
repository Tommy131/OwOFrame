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
namespace owoframe;

use Composer\Autoload\ClassLoader;

use owoframe\exception\ExceptionOutput;
use owoframe\module\ModuleLoader;
use owoframe\object\INI;
use owoframe\utils\Logger;

final class System
{

    /**
     * Android系统标识
     */
    public const OS_ANDROID = 'android';

    /**
     * Linux系统标识
     */
    public const OS_LINUX   = 'linux';

    /**
     * Windows系统标识
     */
    public const OS_WINDOWS = 'windows';

    /**
     * Mac系统标识
     */
    public const OS_MACOS   = 'mac';

    /**
     * BSD系统标识
     */
    public const OS_BSD     = 'bsd';

    /**
     * 未识别的系统标识
     */
    public const OS_UNKNOWN = 'unknown';

    /**
     * ClassLoader实例
     *
     * @access private
     * @var ClassLoader
     */
    private static $classLoader = null;

    private function __construct()
    {
    }

    /**
     * 构造函数
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  ClassLoader|null $classLoader
     */
    public static function init(?ClassLoader $classLoader = null)
    {
        if($classLoader !== null) {
            static::$classLoader = $classLoader;
            $classLoader->addPsr4('application\\', APP_PATH);
            $classLoader->addPsr4('modules\\',     MODULE_PATH);
        }

        // Initialize storages directory folder;
        self::createStorageDirectory();

        // Generate global configuration file;
        self::generateConfig();

        // Set up exception crawling;
        set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
        set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);

        // Define Timezone;
        define('TIME_ZONE', (_global('owo.timeZone', 'Europe/Berlin')));
        date_default_timezone_set(TIME_ZONE);
        ModuleLoader::autoLoad();
    }

    /**
     * 获取客户端信息
     *
     * @author HanskiJay
     * @since  2021-01-10
     * @return string
     */
    public static function getClientBrowser() : string
    {
        if(!empty(server('HTTP_USER_AGENT')))
        {
            $br = server('HTTP_USER_AGENT');
            if(preg_match('/MSIE/i',$br))        $br = 'MSIE';
            elseif(preg_match('/Firefox/i',$br)) $br = 'Firefox';
            elseif(preg_match('/Chrome/i',$br))  $br = 'Chrome';
            elseif(preg_match('/Safari/i',$br))  $br = 'Safari';
            elseif(preg_match('/Opera/i',$br))   $br = 'Opera';
            else $br = 'Other';
            return $br;
        }
        else return '获取浏览器信息失败!';
    }

    /**
     * 获取客户端IP
     *
     * @author HanskiJay
     * @since  2021-01-10
     * @return string
     */
    public static function getClientIp() : string
    {
        if(server('HTTP_CLIENT_IP'))           $ip = server('HTTP_CLIENT_IP');
        elseif(server('HTTP_X_FORWARDED_FOR')) $ip = server('HTTP_X_FORWARDED_FOR');
        elseif(server('REMOTE_ADDR'))          $ip = server('REMOTE_ADDR');
        else                                   $ip = 'Unknown';
        return $ip;
    }

    /**
     * 返回当前系统类型
     *
     * @author HanskiJay
     * @since  2021-02-18
     * @return string
     */
    public static function getOS() : string
    {
        $r  = null;
        $os = php_uname('s');
        if(stripos($os, 'linux') !== false) {
            $r = @file_exists('/system/build.prop') ? self::OS_ANDROID : self::OS_LINUX;
        }
        elseif(stripos($os, 'windows') !== false) {
            $r = self::OS_WINDOWS;
        }
        elseif((stripos($os, 'mac') !== false) || (stripos($os, 'darwin') !== false)) {
            $r = self::OS_MACOS;
        }
        elseif(stripos($os, 'bsd') !== false) {
            $r = self::OS_BSD;
        }
        return $r ?? self::OS_UNKNOWN;
    }

    /**
     * 获取当前PHP的运行模式
     *
     * @author HanskiJay
     * @since  2021-01-30
     * @return string
     */
    public static function getMode() : string
    {
        $sapi = php_sapi_name();
        return !is_string($sapi) ? 'error' : $sapi;
    }

    /**
     * 判断当前的运行模式是否为CLI
     *
     * @author HanskiJay
     * @since  2021-01-30
     * @return boolean
     */
    public static function isRunningWithCLI() : bool
    {
        return strpos(self::getMode(), 'cli') !== false;
    }

    /**
     * 判断当前的运行模式是否为CGI
     *
     * @author HanskiJay
     * @since  2021-01-30
     * @return boolean
     */
    public static function isRunningWithCGI() : bool
    {
        return strpos(self::getMode(), 'cgi') !== false;
    }

    /**
     * 返回指定对象更好的类名
     *
     * @author HanskiJay
     * @since  2021-01-30
     * @param  object      $class 实例化对象
     * @return string
     */
    public static function getShortClassName(object $class) : string
    {
        return basename(str_replace('\\', '/', get_class($class)));
    }

    /**
     * 当前内存使用情况
     *
     * @author HanskiJay
     * @since  2021-11-05
     * @param  boolean $round 四舍五入
     * @param  integer $to    到小数点后面几位
     * @return float
     */
    public static function getCurrentMemoryUsage(bool $round = true, int $to = 2) : float
    {
        $memory = memory_get_usage();
        return $round ? round($memory / 1024 / 1024, $to) : $memory;
    }

    /**
     * 删除文件夹 (包含嵌套)
     *
     * @author HanskiJay
     * @since  2021-04-17
     * @param  string $path 文件夹路径
     * @return boolean
     */
    public static function removeDir(string $path) : bool
    {
        if(!is_dir($path)) return false;
        $path = $path . DIRECTORY_SEPARATOR;
        $dirArray = scandir($path);
        unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);

        foreach($dirArray as $fileName) {
            if(is_dir($path . $fileName)) {
                self::removeDir($path . $fileName);
                if(is_dir($path . $fileName)) {
                    rmdir($path . $fileName);
                }
            } else {
                unlink($path . $fileName);
            }
        }
        rmdir($path);
        return is_dir($path);
    }





    /**
     * 管理区
     */

    /**
     * 创建存储目录文件夹
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return void
     */
    public static function createStorageDirectory() : void
    {
        if(!is_dir(F_CACHE_PATH))  mkdir(F_CACHE_PATH,  755, true);
        if(!is_dir(CONFIG_PATH))   mkdir(CONFIG_PATH,   755, true);
        if(!is_dir(LOG_PATH))      mkdir(LOG_PATH,      755, true);
        if(!is_dir(A_CACHE_PATH))  mkdir(A_CACHE_PATH,  755, true);
        if(!is_dir(RESOURCE_PATH)) mkdir(RESOURCE_PATH, 755, true);
        if(!is_dir(MODULE_PATH))   mkdir(MODULE_PATH,   755, true);
    }

    /**
     * 创建存储目录文件夹
     *
     * @author HanskiJay
     * @since  2022-05-08
     * @return void
     */
    public static function generateConfig() : void
    {
        $ini = new INI(config_path('global.ini'), [
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
            ],
            'view' => [
                'loopLevel'      => 3,
                'judgementLevel' => 3
            ]
        ], true);
        INI::loadObject2Global($ini);
    }

    /**
     * 返回当前框架是否处于调试模式
     *
     * @return boolean
     */
    public static function isDebugMode() : bool
    {
        return changeStr2Bool(_global('owo.debugMode', true));
    }

    /**
     * 返回系统初始化到调用此函数的总共运行时间
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return float
     */
    public static function getRunTime() : float
    {
        return round(microtime(true) - START_MICROTIME, 7);
    }

    /**
     * 返回类加载器
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return ClassLoader|null
     */
    public static function getClassLoader() : ?ClassLoader
    {
        return static::$classLoader;
    }

    /**
     * 设置类加载器
     *
     * @author HanskiJay
     * @since  2022-05-27
     * @param  ClassLoader $classLoader
     * @param  boolean     $forceUpdate
     * @return void
     */
    public static function setClassLoader(ClassLoader $classLoader, bool $forceUpdate = false) : void
    {
        if(($classLoader === null) || $forceUpdate) {
            static::$classLoader = $classLoader;
        }
    }

    /**
     * 返回全局默认的日志记录器
     *
     * @return Logger
     */
    public static function getLogger() : Logger
    {
        static $logger;
        if(!$logger instanceof Logger) {
            $logger = new Logger;
        }
        if(self::isRunningWithCLI()) {
            $logger->fileName = 'owoblog_cli_run.log';
            $logger->logPrefix = 'Console';
        }
        return $logger;
    }

}