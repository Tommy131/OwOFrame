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
 * @Date         : 2023-02-09 22:31:10
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 22:31:10
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



class Session
{
    /**
     * 启动Session
     *
     * @return void
     */
    public static function start() : void
    {
        if(!self::isStarted()) {
            // 设置PHP_SESSION自动过期时间
            ini_set('session.gc_maxlifetime', (string) (@constant('SESSION_EXPIRE_TIME') ?? '10800'));
            session_start();
        }
    }

    /**
     * 判断Session启动状态
     *
     * @constant PHP_SESSION_DISABLED 会话是被禁用的
     * @constant PHP_SESSION_NONE     会话是启用的, 但不存在当前会话
     * @constant PHP_SESSION_ACTIVE   会话是启用的, 而且存在当前会话
     *
     * @return boolean
     */
    public static function isStarted() : bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * 检查是否存在单个Session数据
     *
     * @param  string  $storeKey 存储名
     * @return boolean
     */
    public static function has(string $storeKey) : bool
    {
        return isset($_SESSION[$storeKey]);
    }

    /**
     * 新增一个Session数据
     *
     * @param  string  $storeKey       存储名
     * @param  mixed   $data           数据
     * @param  boolean $rewriteAllowed 是否允许重写
     * @return void
     */
    public static function set(string $storeKey, $data, bool $rewriteAllowed = false) : void
    {
        if(!self::has($storeKey) || $rewriteAllowed) {
            $_SESSION[$storeKey] = $data;
        }
    }

    /**
     * 获取一个Session数据
     *
     * @param  string $storeKey 存储名
     * @param  mixed  $default  默认返回结果
     * @return mixed
     */
    public static function get(string $storeKey, $default = null)
    {
        return $_SESSION[$storeKey] ?? $default;
    }

    /**
     * 获取全部的Session数据
     *
     * @param  string $storeKey 存储名
     * @return array
     */
    public static function getAll() : array
    {
        return $_SESSION ?? [];
    }

    /**
     * 删除单个Session数据
     *
     * @param  string $storeKey 存储名
     * @return void
     */
    public static function delete(string $storeKey) : void
    {
        if(self::has($storeKey)) {
            unset($_SESSION[$storeKey]);
        }
    }

    /**
     * 重置Session数据
     *
     * @return void
     */
    public static function reset() : void
    {
        $_SESSION = [];
    }
}
?>