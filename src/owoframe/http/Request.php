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
 * @Date         : 2023-02-14 18:01:56
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-15 17:44:04
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



class Request
{
    /**
     * 默认用于过滤的表达式
     */
    public const DEFAULT_FILTER =
    [
        "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
        "/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
    ];

    /**
     * Url 原始字符串
     *
     * @var string
     */
    protected $url;


    /**
     * 用于过滤的表达式
     *
     * @var array
     */
    protected $filter = self::DEFAULT_FILTER;


    public function __construct(?string $url = null)
    {
        $this->url = $url ?? \owo\get_raw_path();
    }

    /**
     * 返回 Url
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * 设置 Url
     *
     * @param  string $url
     * @return Request
     */
    public function setUrl(string $url) : Request
    {
        $this->__construct($url);
        return $this;
    }

    /**
     * 判断Url是否有效
     *
     * @return boolean
     */
    public function isValid() : bool
    {
        return \owo\str_is_url($this->url);
    }

    /**
     * 重置过滤器
     *
     * @param  boolean $setDefault
     * @return Request
     */
    public function resetFilter(bool $setDefault = true) : Request
    {
        $this->filter = $setDefault ? self::DEFAULT_FILTER : [];
        return $this;
    }

    /**
     * 合并过滤表达式
     *
     * @param  array $filter
     * @return Request
     */
    public function setFilter(array $filter) : Request
    {
        $this->filter = array_merge($this->filter, $filter);
        return $this;
    }

    /**
     * 字符串请求过滤
     *
     * @param  string $str
     * @param  string $allowedHTML 允许的HTML标签 (e.g. "<a><b><div>" (将不会过滤这三个HTML标签))
     * @return Request
     */
    public function filter(string &$str, string $allowedHTML = null) : Request
    {
        $str = preg_replace($this->filter, '', strip_tags($str, $allowedHTML));
        return $this;
    }

    /**
     * TODO: 计划编写以下方法:
     * ~ 请求头解析
     * ~ 获取请求资源类型
     * ~ 来源域名记录
     * ~ 获取请求真实IP
     * ~ ...
     */

    /**
     * 魔术方法
     *
     * @param  string $name
     * @return void
     */
    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }
}
?>