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
 * @Date         : 2023-02-20 20:09:31
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-20 21:29:55
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



use owoframe\http\Curl;

class Graphql
{
    /**
     * 原始数据
     *
     * @var string
     */
    protected $operationName, $query;

    /**
     * 参数
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Curl请求类
     *
     * @var Curl
     */
    protected $curl;

    /**
     * 请求Url
     *
     * @var string
     */
    protected $url;

    /**
     * 超时时间 (秒)
     *
     * @var integer
     */
    protected $timeout = 10;

    /**
     * 执行请求前的回调方法
     *
     * @var callable
     */
    protected $callback;

    /**
     * 编码请求头
     *
     * @var string|null
     */
    protected $encoded = null;

    /**
     * 请求状态
     *
     * @var boolean
     */
    protected $processed = false;

    /**
     * 处理结果
     *
     * @var mixed
     */
    protected $result = null;


    /**
     * 设置操作名称
     *
     * @param  string  $name
     * @return Graphql
     */
    public function setOperationName(string $name) : Graphql
    {
        $this->operationName = $name;
        return $this;
    }

    /**
     * 设置变量名称
     *
     * @param  array   $variables
     * @return Graphql
     */
    public function setVariables(array $variables) : Graphql
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    /**
     * 设置请求语句
     *
     * @param  string  $query
     * @return Graphql
     */
    public function setQuery(string $query) : Graphql
    {
        $this->query = $query;
        return $this;
    }

    /**
     * 设置Curl请求头
     *
     * @param  Curl|null $curl
     * @param  boolean   $update
     * @return Graphql
     */
    /** */
    public function setCurl(?Curl $curl = null, bool $update = false) : Graphql
    {
        if((!$this->curl instanceof Curl) || $update) {
            $this->curl = !is_null($curl) ? $curl : (new Curl);
        }
        return $this;
    }

    /**
     * 返回Curl实例
     *
     * @param  boolean   $autoCreate
     * @return Curl|null
     */
    public function getCurl(bool $autoCreate = false) : ?Curl
    {
        return ($this->curl instanceof Curl) ? $this->curl : ($autoCreate ? $this->setCurl()->curl : null);
    }

    /**
     * 设置请求Url
     *
     * @param  string  $url
     * @return Graphql
     */
    public function setRequestUrl(string $url) : Graphql
    {
        if($this->curl instanceof Curl) {
            $this->curl->setUrl($url);
        }
        $this->url = $url;
        return $this;
    }

    /**
     * 设置请求Url
     *
     * @param  integer $timeout
     * @return Graphql
     */
    public function setTimeout(int $timeout) : Graphql
    {
        if($this->curl instanceof Curl) {
            $this->curl->setTimeout($timeout);
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * 设置执行请求前的回调方法
     *
     * @param  callable $callback
     * @return Graphql
     */
    public function setBeforeQueryCallback(callable $callback) : Graphql
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * 使用JSON编码请求包
     *
     * @return string
     */
    public function encode() : string
    {
        $this->encoded = json_encode([
            'operationName' => $this->operationName,
            'variables'     => $this->variables ?? [],
            'query'         => $this->query
        ]);
        return $this->encoded;
    }

    /**
     * 设置已经发送请求
     *
     * @return Graphql
     */
    public function setProcessed() : Graphql
    {
        $this->processed = true;
        return $this;
    }

    /**
     * 返回请求状态
     *
     * @return boolean
     */
    public function processed() : bool
    {
        return $this->processed;
    }

    public function query(bool $autoQuery = true) : Graphql
    {
        // 初始化Curl请求
        $this->getCurl(true)
        ->setUrl($this->url)
        ->setTimeOut($this->timeout)
        ->setContentType('application/json; charset=UTF-8')
        ->setPostDataRaw($this->encode());

        // 调用回调
        if(is_callable($this->callback)) {
            $content = call_user_func_array($this->callback, [$this]) ?? null;
        }

        if(!$content || ($autoQuery && !$this->processed())) {
            $content = $this->getCurl()->exec()->getContent($h);
        }

        if(is_string($content)) {
            $content = json_decode($content);
        }
        $this->result = $content;
        return $this;
    }

    /**
     * 获取请求结果
     *
     * @return mixed
     */
    public function getResult()
    {
        $this->result = $this->result->data ?? $this->result;
        return $this->result;
    }

    /**
     * 重置载体
     *
     * @return Graphql
     */
    public function reset() : Graphql
    {
        $this->operationName = $this->query = $this->curl = $this->url = $this->callback = $this->encoded = $this->result = null;
        $this->variables = [];
        $this->timeout   = 10;
        $this->processed = false;
        return $this;
    }
}
?>