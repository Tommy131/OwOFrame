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
namespace owoframe\http;

class Cookie
{

    /**
     * Cookie前缀
     *
     * @access private
     * @var string
     */
    private static $_prefix = '';

    /**
     * Cookie存储路径
     *
     * @access private
     * @var string
     */
    private static $_path = '/';



    /**
     * 设置Cookie前缀
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  string      $url 地址
     * @return void
     */
    public static function setPrefix(string $url) : void
    {
        self::$_prefix = md5($url);
        $parsed = parse_url($url);

        /** 在路径后面强制加上斜杠 */
        self::$_path = $parsed['path'] . '/';
    }

    /**
     * 返回Cookie前缀设置
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return string
     */
    public static function getPrefix() : string
    {
        return self::$_prefix;
    }

    /**
     * 返回Cookie路径设置
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return string
     */
    public static function getPath() : string
    {
        return self::$_path;
    }

    /**
     * 返回所有Cookies
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return array
     */
    public static function getAll() : array
    {
        return $_COOKIE ?? [];
    }

    /**
     * 获取一个Cookie
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  string      $key     键名
     * @param  mixed       $default 默认返回结果
     * @return mixed
     */
    public static function get(string $key, $default = NULL)
    {
        $key = self::$_prefix . $key;
        $value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : (isset($_POST[$key]) ? $_POST[$key] : $default);
        return is_array($value) ? $default : $value;
    }

    /**
     * 判断是否存在一个Cookie
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  string      $key 键名
     * @return boolean
     */
    public static function has(string $key) : bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * 设置一个Cookie
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  string      $key    键名
     * @param  mixed       $value  键值
     * @param  integer     $expire 过期时间
     */
    public static function set(string $key, $value, $expire = 0) : void
    {
        $key = self::$_prefix . $key;
        setrawcookie($key, rawurlencode($value), $expire, self::$_path);
        $_COOKIE[$key] = $value;
    }

    /**
     * 删除一个Cookie
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @param  string      $key 键名
     * @return boolean
     */
    public static function delete(string $key) : bool
    {
        $key = self::$_prefix . $key;
        if (!isset($_COOKIE[$key])) {
            return false;
        }

        setcookie($key, '', time() - 2592000, self::$_path);
        unset($_COOKIE[$key]);
        return true;
    }
}
?>