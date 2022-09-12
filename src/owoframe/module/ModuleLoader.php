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
namespace owoframe\module;

use owoframe\System;
use owoframe\object\INI;
use owoframe\object\Priority;

class ModuleLoader
{
    /**
     * 模块信息识别文件名称
     */
    public const IDENTIFY_FILE_NAME = 'info.ini';

    /**
     * 模块池
     *
     * @access private
     * @var array
     */
    private static $modulePool = [];

    /**
     * 优先级加载列表
     *
     * @var array
     */
    private static $priorityLoadList = [];


    /**
     * 自动从加载路径加载模块
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @return void
     */
    public static function autoLoad() : void
    {
        $dirArray = scandir(MODULE_PATH);
        // unset dots and pathname;
        unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);
        $path = [];
        foreach($dirArray as $name) {
            if(is_dir($dir = MODULE_PATH . $name . DIRECTORY_SEPARATOR) && is_file($dir . self::IDENTIFY_FILE_NAME)) {
                $path[$name] = $dir;
            }
        }
        foreach($path as $name => $dir) {
            self::loadModule($dir, $name);
        }

        $_ = static::$priorityLoadList[Priority::ABS_HIGHEST] ?? null;
        if($_ && (count($_) > 1)) {
            System::getLogger()->error('ModuleLoader > Failed to load all modules! Priority level [ABS_HIGHEST] only one module is allowed!', true);
            return;
        }
        unset(static::$priorityLoadList[Priority::ABS_HIGHEST]);

        $module = array_shift($_);
        $module = self::getModule($module);
        $module->onEnable();
        $module->setEnabled();

        sort(static::$priorityLoadList);
        foreach(static::$priorityLoadList as $priority => $list) {
            shuffle($list);
            foreach($list as $name) {
                $module = self::getModule($name);
                $module->onEnable();
                $module->setEnabled();
            }
        }
    }

    /**
     * 判断模块是否存在
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @return boolean
     */
    public static function existsModule(string $name, &$info = null) : bool
    {
        if(self::getModule($name)) return true;
        // Start judgment;
        $hisPath = MODULE_PATH . $name . DIRECTORY_SEPARATOR;
        if(!is_dir($hisPath) || !file_exists($ic = $hisPath . self::IDENTIFY_FILE_NAME)) return false;
        $info = new INI($ic);
        if(!self::checkInfo($info->getAll())) return false;
        $info = $info->obj(); // Format to JSON Object;
        if(!Priority::has((int) $info->priority)) return false;
        if(!file_exists($hisPath . $info->className . '.php')) return false;
        /*$info->className = str_replace('/', '\\', trim($info->className));
        if(!class_exists($info->className)) return false;
        if(is_bool($c = (new \ReflectionClass($info->className))->getParentClass())) return false;
        if($c->getName() !== ModuleBase::className) return false;*/
        if(isset($info->onlyCLI) && $info->onlyCLI && !System::isRunningWithCLI()) return false;
        // End judgment;
        return true;
    }

    /**
     * 获取模块实例化对象
     *
     * @author HanskiJay
     * @since  2021-02-08
     * @param  string      $name 模块名称
     * @return ModuleBase|null
     */
    public static function getModule(string $name) : ?ModuleBase
    {
        return static::$modulePool[strtolower($name)] ?? null;
    }

    /**
     * 加载模块
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @param  string  $dir  模块所在的路径
     * @param  string  $name 模块名称
     * @return boolean
     */
    public static function loadModule(string $dir, string $name) : bool
    {
        if(self::existsModule($name, $info)) {
            // include_once($dir . $info->className . '.php');
            $namespace = $info->namespace ?? '';
            $class     = $namespace . '\\' . $info->className;

            if(class_exists($class)) {
                static::$priorityLoadList[$info->priority][] = strtolower($info->name);
                $class = static::$modulePool[strtolower($info->name)] = new $class($dir, $info);
                $class->onLoad();
                return true;
            } else {
                System::getLogger()->error("ModuleLoader > Load module '{$info->name}' failed: The NameSpace or ClassName may incorrect!");
            }
        } else {
            System::getLogger()->error("ModuleLoader > Module '{$name}' not exists! Please check the module information file '" . self::IDENTIFY_FILE_NAME . "' !");
        }
        return false;
    }

    /**
     * 卸载模块
     *
     * @author HanskiJay
     * @since  2021-02-08
     * @param  string      $name 模块名称
     * @return boolean
     */
    public static function disableModule(string $name) : bool
    {
        $name = strtolower($name);
        if(($module = self::getModule($name)) !== null) {
            $module->onDisable();
            $module->setDisabled();
            unset(static::$modulePool[$name]);
        }
        return false;
    }

    /**
     * 检查模块信息文件是否有效
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @param  array      $info 已加载的配置文件
     * @return boolean
     */
    public static function checkInfo(array $info, string &$missParam = '') : bool
    {
        return checkArrayValid($info, ['author', 'className', 'name', 'description', 'version', 'priority'], $missParam);
    }
}