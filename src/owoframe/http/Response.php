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
 * @Date         : 2023-02-14 15:03:49
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-19 23:23:44
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



use Closure;
use JsonSerializable;

use owoframe\event\http\BeforeResponseEvent;
use owoframe\event\http\AfterResponseEvent;
use owoframe\event\system\OutputEvent;
use owoframe\http\HttpCode;
use owoframe\utils\DataEncoder;
use owoframe\utils\MIMEType;

class Response
{
    /**
     * 响应 & 数据发送状态
     *
     * @access protected
     * @var boolean
     */
    protected $hasSent = false;

    /**
     * 回调参数(可以输出数据的回调方法)
     *
     * @access protected
     * @var callable|null
     */
    protected $callback;

    /**
     * 携带的参数
     *
     * @access protected
     * @var array
     */
    protected $callParams;

    /**
     * HTTP响应代码
     *
     * @access protected
     * @var integer
     */
    protected $code = 502;

    /**
     * HTTP header参数设置
     *
     * @access protected
     * @var array
     */
    protected $header =
    [
        'Content-Type'           => 'text/html; charset=utf-8',
        'X-Content-Type-Options' => 'nosniff',
        'Pragma'                 => 'HTTP/1.0'
    ];

    /**
     * 准备发送的字符串数据
     *
     * @access protected
     * @var array|string
     */
    protected $prepareSendData = null;


    /**
     * 返回回调方法的有效性
     *
     * @return boolean
     */
    public function isCallable() : bool
    {
        return is_callable($this->callback);
    }

    /**
     * 设置回调
     *
     * @param  callable|null $callback
     * @param  array         $params
     * @return void
     */
    public function __construct(?callable $callback = null, array $params = [])
    {
        $this->setCallback($callback, $params);
    }

    /**
     * 设置回调
     *
     * @param  callable|null $callback
     * @param  array         $params
     * @return Response
     */
    public function setCallback(?callable $callback = null, array $params = []) : Response
    {
        $this->callback   = $callback;
        $this->callParams = $params;
        return $this;
    }

    /**
     * 设置准备发送的字符串数据
     *
     * @param  array|string $prepareSendData
     * @return Response
     */
    public function setPrepareSendData($prepareSendData) : Response
    {
        $this->prepareSendData = $prepareSendData;
        return $this;
    }

    /**
     * 返回相应代码
     *
     * @return integer
     */
    public function getResponseCode() : int
    {
        return $this->code;
    }

    /**
     * 设置响应代码
     *
     * @param  integer  $code
     * @return Response
     */
    public function setResponseCode(int $code) : Response
    {
        if(HttpCode::has($code)) {
            $this->code = $code;
        }
        return $this;
    }

    /**
     * 设置响应头参数
     *
     * @param  string   $name
     * @param  mixed    $value
     * @return Response
     */
    public function setHeader(string $name, $value) : Response
    {
        $this->header[$name] = $value;
        return $this;
    }

    /**
     * 设置响应头参数
     *
     * @param  array    $header
     * @return Response
     */
    public function mergeHeader(array $header) : Response
    {
        $this->header = array_merge($this->header, $header);
        return $this;
    }

    /**
     * 设置响应内容格式
     *
     * @param  string   $type
     * @return Response
     */
    public function setContentType(string $type) : Response
    {
        $this->setHeader('Content-Type', $type);
        return $this;
    }

    /**
     * 返回响应状态
     *
     * @return boolean
     */
    public function hasSent() : bool
    {
        return $this->hasSent || headers_sent();
    }

    /**
     * 发送响应头
     *
     * @return boolean
     */
    protected function sendHeader(int $length = 0, bool $isJson = false) : bool
    {
        if($this->hasSent()) {
            return false;
        }

        // 发送头部信息
        foreach($this->header as $k => $v) {
            header("{$k}: {$v}");
        }

        if($isJson) {
            header('Content-Type: ' . MIMEType::get('json'));
        }

        $length += strlen(ob_get_contents() ?? '');
        header('Powered-By: OwOFrame v' . OWO_VERSION);
        header('GitHub-Page: ' . GITHUB_PAGE);
        header('Content-Length: ' . $length);

        if(HttpCode::has($this->code)) {
            header((\owo\server('SERVER_PROTOCOL') ?? 'HTTP/1.1') . " {$this->code} " . HttpCode::ALL[$this->code], true, $this->code);
            http_response_code($this->code);
        }
        return true;
    }

    /**
     * 返回调用响应值
     *
     * @param  $isJson
     * @return string|null
     */
    public function call(&$isJson) : ?string
    {
        $isJson = false;
        $called = null;
        if(!is_callable($this->callback)) {
            if(is_array($this->prepareSendData)) {
                $called = json_encode($this->prepareSendData, JSON_UNESCAPED_UNICODE);
                $isJson = true;
            }
            elseif(is_string($this->prepareSendData)) {
                $called = $this->prepareSendData;
            }
        }
        elseif($this->callback instanceof Closure) {
            $called = $this->callback;
            $called = (string) $called(...$this->callParams);
        } else {
            $called = call_user_func_array($this->callback, $this->callParams);
            if(is_array($called) || ($called instanceof JsonSerializable)) {
                $called = json_encode($called, JSON_UNESCAPED_UNICODE);
                $isJson = true;
            }
        }

        if(!is_string($called)) {
            $called = new DataEncoder;
            $called = $called->setStandardData(500, 'No output', false)->encode();
            $isJson = true;
        }
        return $called ?? null;
    }

    /**
     * 发送响应载体
     *
     * @param  boolean  $showRuntime
     * @return Response
     */
    public function send(bool $showRuntime = false) : Response
    {
        // 触发发送响应载体前事件
        (new BeforeResponseEvent)->trigger();

        // 输出运行时间 (如果允许)
        if($showRuntime) {
            \owo\html_runtime();
        }
        $data = $this->call($isJson);

        if($this->sendHeader(strlen($data), $isJson))
        {
            $event = new OutputEvent($data);
            $event->trigger();
            $event->output();

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            if(function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            $this->hasSent = true;

            // 触发发送响应载体后事件
            (new AfterResponseEvent)->trigger();
        }
        return $this;
    }

    /**
     * 魔术方法
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->header[$name] ?? null;
    }
}
?>