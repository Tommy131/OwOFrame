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
namespace owoframe\utils;

class Str
{
    /**
     * 判断字符串是否为多级域名格式
     *
     * @author HanskiJay
     * @since  2021-01-30
     * @param  string $str    字符串
     * @param  array  &$match 允许的一级域名
     * @return boolean
     */
    public static function isDomain(string $str, array &$match = ['localhost']) : bool
    {
        $str = str_replace(['http', 'https', '://'], '', trim($str));
        if(in_array($str, $match)) {
            $match = $str;
            return true;
        }
        // * Regex verified: https://regex101.com/r/rhSD1e/1;
        return (strpos($str, '--') === false) && (bool) preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?[\.]*)+?([a-z]+)$/i', $str, $match);
    }

    /**
     * 判断字符串是否为Url地址
     *
     * @param  string $str 字符串
     * @return boolean
     */
    public static function isUrl(string $str) : bool
    {
        return (bool) preg_match('/^((http|https):\/\/)?\w+\.\w+\//iU', $str);
    }

    /**
     * 简单判断字符串是否为邮箱格式
     *
     * @author HanskiJay
     * @since  2021-11-05
     * @param  string  $str
     * @param  string   &$suffix 允许匹配的域名后缀 (e.g.: $suffix = 'com.com.cn|abc.cn'), 匹配完成后传入匹配结果到此参数
     * @return boolean
     */
    public static function isEmail(string $str, string &$suffix = '') : bool
    {
        $preset = 'com|org|net|com.cn|org.cn|net.cn|cn';
        // Judgement for the allowed suffix format;
        if(preg_match('/[a-z.|]+/i', $suffix)) {
            $preset .= '|' . $suffix;
        }
        $preset = str_replace('.', '\.', $preset);
        return (bool) preg_match('/^([\w+\-.]+)@([a-z0-9\-.]+)\.(' . $preset . ')$/i', trim($str), $suffix);
    }

    /**
     * 生成随机字符串
     *
     * @author HanskiJay
     * @since  2021-11-05
     * @param  integer $length       生成长度
     * @param  boolean $specialChars 是否加入符号
     * @return string
     */
    public static function randomString(int $length, bool $specialChars = false) : string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if($specialChars) {
            $chars .= '!@#$%^&*()';
        }

        $result = '';
        $max = strlen($chars) - 1; // 字符串指针从0开始, 也就是长度[$length - 1]；
        for($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, $max)];
        }
        return $result;
    }

    /**
     * 创建一个随机的UUID
     *
     * @author HanskiJay
     * @since  2022-01-08
     * @return string
     */
    public static function generateUUID() : string
    {
        $str   = md5(uniqid(self::randomString(5), true));
        $uuid  = '';
        $array = [0, 8, 12, 16, 20];
        foreach([8, 4, 4, 4, 12] as $k => $v) {
            $uuid .= substr($str, $array[$k], $v) . '-';
        }
        $uuid = rtrim($uuid, '-');
        return $uuid;
    }

    /**
     * 判断传入的字符串是否仅为字母和数字
     *
     * @author HanskiJay
     * @since  2021-02-11
     * @param  string      $str   传入的字符串
     * @param  &match      $match 匹配结果
     * @return boolean
     */
    public static function isOnlyLettersAndNumbers(string $str, &$match = null) : bool
    {
        return (bool) preg_match("/^[A-Za-z0-9]+$/", $str, $match);
    }

    /**
     * 转义字符串中的斜杠
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @param  string      &$str 所需字符串
     * @return string
     */
    public static function escapeSlash(string &$str) : string
    {
        return $str = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $str);
    }

    /**
     * 返回HTML标签与换行
     *
     * @author HanskiJay
     * @since  2022-08-03
     * @param  string $searchString
     * @param  string $globalString
     * @return string
     */
    public static function findTagNewline(string $searchString, string $globalString) : string
    {
        $tag = str_replace(['.', '/', '|', '$'], ['\.', '\/', '\|', '\$'], $searchString);
        if(preg_match("/(\s*?){1}{$tag}/i", $globalString, $m)) {
            return $m[0];
        }
        return $searchString;
    }
}