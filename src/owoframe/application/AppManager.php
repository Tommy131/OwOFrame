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

use FilesystemIterator as FI;

use owoframe\exception\InvalidAppException;
use owoframe\exception\ClassNotFoundException;

use owoframe\http\HttpManager as Http;

class AppManager
{
    /**
     * AppBase basic namespace
     */
    public const BASIC_CLASS = "owoframe\\application\\AppBase";


    private function __construct()
    {
    }

    /**
     * 判断是否存在一个Application
     *
     * @author HanskiJay
     * @since  2021-01-26
     * @param  string      $appName app名称
     * @param  &           &$class  向上传递存在的应用对象
     * @return boolean
     */
    public static function hasApp(string $appName, &$class = null) : bool
    {
        $name    = strtolower($appName);
        $appName = ucfirst($name);
        $class   = "\\application\\{$name}\\{$appName}" . 'App';

        if(!class_exists($class)) {
            return false;
        }
        if((new \ReflectionClass($class))->getParentClass()->getName() !== self::BASIC_CLASS) {
            throw new InvalidAppException($appName, 'Parent class should be interfaced by ' . self::BASIC_CLASS);
        }
        return true;
    }

    /**
     * 获取默认端App
     *
     * @author HanskiJay
     * @since  2020-09-09
     * @return AppBase|null
     */
    public static function getDefaultApp() : ?AppBase
    {
        return self::getApp(_global('owo.defaultApp'));
    }

    /**
     * 获取指定App
     *
     * @author HanskiJay
     * @since  2020-09-09
     * @param  string      $appName App名称
     * @return AppBase|null
     */
    public static function getApp(string $appName) : ?AppBase
    {
        static $application;
        if(isset($application[$appName]) && $application[$appName] instanceof AppBase) {
            return $application[$appName];
        }

        if(self::hasApp($appName, $class)) {
            return $application[$appName] = new $class(Http::getCompleteUrl());
        }
        return null;
    }


    public static function initializeApplications() : void
    {
        $path = new FI(APP_PATH, FI::KEY_AS_PATHNAME | FI::KEY_AS_FILENAME | FI::SKIP_DOTS);
        foreach($path as $info) {
            if(is_dir($info->getPathName())) {
                $appName = $info->getFileName();
                // 过滤文件名;
                if(!preg_match('/[a-z0-9]+/i', $appName)) continue;
                self::getApp($appName);
            }
        }
    }
}