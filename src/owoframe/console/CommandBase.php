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
 * @Date         : 2023-02-15 18:22:59
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-15 18:22:59
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console;


use owoframe\System;
use owoframe\utils\Logger;

abstract class CommandBase
{
    /**
     * 触发该指令时调用此方法执行指令
     *
     * @param  array   $params
     * @return boolean
     */
    abstract public function execute(array $params) : bool;

    /**
     * 返回该指令的所有别名
     *
     * @return array
     */
    abstract public static function getAliases() : array;

    /**
     * 返回指令名称
     *
     * @return string
     */
    abstract public static function getName() : string;

    /**
     * 返回至零点描述
     *
     * @return string
     */
    abstract public static function getDescription() : string;

    /**
     * 返回使用描述
     *
     * @return string
     */
    public static function getUsage() : string
    {
        return 'owo ' . static::getName();
    }

    /**
     * 给出加载器获悉是否自动加载并注册此指令
     *
     * @return boolean
     */
    public static function autoLoad() : bool
    {
        return true;
    }

    /**
     * 返回日志实例
     *
     * @return Logger
     */
    public function getLogger() : Logger
    {
        return System::getMainLogger();
    }
}
?>