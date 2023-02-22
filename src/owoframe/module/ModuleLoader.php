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
 * @Date         : 2023-02-02 21:05:15
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-21 17:36:32
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\module;



use FilesystemIterator as FI;
use SplFileInfo;
use owoframe\Priority;

class ModuleLoader
{
    /**
     * 识别模块配置文件名称
     */
    public const IDENTIFY_NAME = 'info.json';

    /**
     * 默认优先级
     */
    public const DEFAULT_PRIORITY = Priority::NORMAL;

    /**
     * 加载路径
     *
     * @access protected
     * @var string
     */
    protected static $loadPath = '';

    /**
     * 模块池
     *
     * @access protected
     * @var array
     */
    protected static $modulePool = [];

    /**
     * 优先级加载列表
     *
     * @access protected
     * @var array
     */
    protected static $priorityLoadList = [];


    private function __construct()
    {
    }


    /**
     * 设置或返回加载路径
     *
     * @param  string $loadPath
     * @return string
     */
    public static function loadPath(string $loadPath = '') : string
    {
        if(is_dir($loadPath)) {
            static::$loadPath = $loadPath;
        }
        return static::$loadPath;
    }

    /**
     * 检测有效性
     *
     * @param  object $info
     * @return boolean
     */
    public static function checkValidity(object &$info) : bool
    {
        // 检查配置文件是否存在关键值
        if(!\owo\array_check_validity((array) $info, ['class', 'skipLoad', 'allowedIn', 'compatible', 'path'])) {
            return false;
        }
        // 检查配置文件的有效性
        if($info->skipLoad || !in_array(OWO_VERSION, $info->compatible) || !in_array(\owo\php_current(), $info->allowedIn)) {
            return false;
        }

        $info->class = str_replace(['{namespace}', 'namespace'], $info->namespace, $info->class);
        if(!class_exists($info->class) && !is_a($info->class, ModuleBase::class, true)) {
            return false;
        }
        return true;
    }

    /**
     * 通过获取模块的配置文件加载模块
     *
     * @param  SplFileInfo $fi
     * @return boolean
     */
    public static function loadModule(SplFileInfo $fi) : bool
    {
        $info = file_get_contents($fi->getRealPath());
        $info = json_decode($info ? $info : '');
        if(!$info) return false;

        // 检查模块是否已被加载
        $name = strtolower($info->displayName);
        if(self::getModule($name)) return true;

        // 写入模块路径到信息
        $info->path = $fi->getPath() . DIRECTORY_SEPARATOR;

        // 利用 Composer 的 ClassLoader 自动加载类
        $info->namespace = $info->namespace ?? \owo\str_split($info->path, null, DIRECTORY_SEPARATOR);
        $info->namespace = is_array($info->namespace) ? end($info->namespace) : $info->namespace;
        $info->namespace = "module\\{$info->namespace}\\";
        \owo\get_class_loader()->addPsr4($info->namespace, $info->path . 'src/');

        // 检查有效性
        if(self::checkValidity($info))
        {
            $priority =& static::$priorityLoadList;

            $priority[$info->priority ?? self::DEFAULT_PRIORITY][] = $name;
            $class = $info->class;
            $class = static::$modulePool[$name] = new $class($info->path, $info);
            $class->onLoad();
            return true;
        }
        return false;
    }

    /**
     * 获取模块实例化对象
     *
     * @param  string $name 模块名称
     * @return ModuleBase|null
     */
    public static function getModule(string $name) : ?ModuleBase
    {
        return static::$modulePool[strtolower($name)] ?? null;
    }

    /**
     * 初始化加载
     *
     * @access private
     * @param  string $path
     * @return void
     */
    private static function init(?string $path = null) : void
    {
        $path = new FI($path ?? static::loadPath(), FI::KEY_AS_FILENAME | FI::SKIP_DOTS);

        foreach($path as $info)
        {
            // 过滤 .(.*) | LICENSE | README(.*).* 文件
            if(preg_match('/^\..*|LICENSE|README\w+\.md$/iuU', $info->getFilename())) {
                continue;
            }

            if($info->isDir()) {
                self::init($info->getRealPath());
            } else {
                if($info->getFilename() === self::IDENTIFY_NAME) {
                    self::loadModule($info);
                }
            }
        }
    }

    /**
     * 自动加载模块
     *
     * @param  string $path
     * @return void
     */
    public static function autoLoad(?string $path = null) : void
    {
        self::init($path);
        $_  =& static::$priorityLoadList;

        ksort($_);
        foreach($_ as $list)
        {
            shuffle($list);
            foreach($list as $name) {
                $module = self::getModule($name);
                $module->onEnable();
                $module->setEnabled();
            }
        }
    }

    /**
     * 卸载模块
     *
     * @param  string $name 模块名称
     * @return boolean
     */
    public static function disableModule(string $name) : bool
    {
        $name   = strtolower($name);
        $module = self::getModule($name);
        if($module !== null) {
            $module->onDisable();
            $module->setDisabled();
            unset(static::$modulePool[$name]);
        }
        return false;
    }
}
?>