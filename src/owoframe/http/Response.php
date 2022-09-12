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

use Closure;
use JsonSerializable;
use ReflectionClass;

use owoframe\System;

use owoframe\utils\MIMEType;

use owoframe\event\http\BeforeResponseEvent;
use owoframe\event\http\AfterResponseEvent;
use owoframe\event\system\OutputEvent;

use owoframe\utils\DataEncoder;
use owoframe\utils\Logger;

class Response
{
    /**
     * 响应 & 数据发送状态
     *
     * @access private
     * @var boolean
     */
    private $hasSent = false;

    /**
     * 回调参数(可以输出数据的回调方法)
     *
     * @access private
     * @var callable
     */
    private $callback;

    /**
     * HTTP响应代码(Default:200)
     *
     * @access protected
     * @var integer
     */
    protected $code = 200;

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
     * 是否显示运行时间
     *
     * @var boolean
     */
    public static $showRuntimeDiv = true;

    /**
     * 默认响应信息
     *
     * @var string
     */
    public $defaultResponseMsg = '[OwOResponseError] Unknown Error...';



    public function __construct(?callable $callback, array $params = [])
    {
        $this->callback   = $callback;
        $this->callParams = $params;
    }

    /**
     * 设置回调
     *
     * @author HanskiJay
     * @since  2021-04-16
     * @param  callable   $callback 可回调参数
     * @param  array      $params   回调参数传递
     * @return Response
     */
    public function setCallback(callable $callback, array $params = []) : Response
    {
        $this->__construct($callback, $params);
        return $this;
    }

    /**
     * 设置HTTP响应代码
     *
     * @author HanskiJay
     * @since  2020-09-10 18:49
     * @param  int      $code 响应代码
     * @return Response
     */
    public function setResponseCode(int $code) : Response
    {
        if(isset(HttpManager::HTTP_CODE[$code])) {
            $this->code = $code;
        }
        return $this;
    }

    /**
     * 发送响应头
     *
     * @author HanskiJay
     * @since  2021-02-09
     * @return void
     */
    public function sendResponse() : void
    {
        $logger = new Logger;
        $logger->logPrefix = 'HTTP/Response';
        (new BeforeResponseEvent)->trigger();
        $isJson = false;

        // If the callback is invalid;
        if(!is_callable($this->callback)) {
            $this->callback = [$this, 'defaultResponseMsg'];
        }


        // Judgement whether the callback is Closure;
        if($this->callback instanceof Closure) {
            $called = $this->callback;
            $called = $called();
        } else {
            // Callback method and get result;
            $called = call_user_func_array($this->callback, $this->callParams);
            if(is_array($called) || ($called instanceof JsonSerializable)) {
                $called = json_encode($called, JSON_UNESCAPED_UNICODE);
                $isJson = true;
            }
            elseif($called instanceof DataEncoder) {
                $called = $called->encode();
                $isJson = true;
            } else {
                $reflect = new ReflectionClass($this->callback[0]);
                if($reflect->implementsInterface(StandardOutputConstant::class)) {
                    $called = $this->callback[0]->getOutput();
                }
            }
        }

        // Judgement whether the callback is null;
        $called = is_null($called) ? '' : $called;

        // Check whether the callback result type is String;
        if(!is_string($called)) {
            $json = new DataEncoder();
            $called = $json->setStandardData(($this->code !== 200) ? $this->code : 502, 'Failed to response data.', false)->mergeData([
                'handler'      => 'OwOResponseError',
                'issueClass'   => get_class($this->callback[0]),
                'issueMethod'  => $this->callback[1],
                'expectedType' => 'string|array|null',
                'actualType'   => gettype($called)
            ])->encode();
            $logger->debug($called);
            $isJson = true;
        }

        if($isJson) $this->header('Content-Type', MIMEType::MIMETYPE['json']);
        // Judgement whether the output is JSON format;
        self::getRuntimeDiv(!$isJson && static::$showRuntimeDiv);

        // set HTTP-HEADER;
        if(!headers_sent() && !empty($this->header)) {
            foreach($this->header as $name => $val) {
                header($name . (!is_null($val) ? ": {$val}"  : ''));
            }
            $length = strlen($called) + strlen(ob_get_contents());
            header('Powered-By: OwOFrame v' . FRAME_VERSION);
            header('OwO-Author: HanskiJay');
            header('GitHub-Page: ' . GITHUB_PAGE);
            header('Content-Length: ' . $length);
            HttpManager::setStatusCode($this->code);
        }

        $event = new OutputEvent($called);
        $event->trigger();
        $event->output();
        ob_end_flush();

        if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
        (new AfterResponseEvent)->trigger();
        $this->hasSent = true;

        $logger->debug("[{$this->code}] Status: Sent; Length: " . $length);
    }

    /**
     * 获取当前设置的HTTP响应代码
     *
     * @author HanskiJay
     * @since  2020-09-10 18:49
     * @param  int      $code 响应代码
     * @return int
     */
    public function getResponseCode(int $code = 403) : int
    {
        return $this->code ?: $code;
    }

    /**
     * 设置HTTP_HEADER
     *
     * @author HanskiJay
     * @since  2020-09-10 18:49
     * @param  string      $index 文件/文件夹索引
     * @param  string      $val 值
     * @return mixed
     */
    public function &header(string $name, string $val = '')
    {
        if(($name === '') && ($val === '')) {
            return $this->header;
        }
        elseif(isset($this->header[$name])) {
            $splitStr = '@';
            $vars     = explode($splitStr, $val);
            $val      = array_shift($vars);
            $mode     = strtolower(array_shift($vars) ?? 'set');
            if(($mode === 'set') || ($mode === 'update')) {
                $this->header[$name] = $val;
                return $this->header[$name];
            } else {
                return $this->header[$name];
            }
        } else {
            $this->header[$name] = $val;
            return $this->header[$name];
        }
    }

    /**
     * 返回响应状态
     *
     * @author HanskiJay
     * @since  2021-03-21
     * @return boolean
     */
    public function hasSent() : bool
    {
        return $this->hasSent;
    }

    /**
     * 默认响应信息
     *
     * @author HanskiJay
     * @since  2021-03-21
     * @return string
     */
    public function defaultResponseMsg() : string
    {
        return $this->defaultResponseMsg;
    }

    /**
     * 输出运行时间框
     *
     * @author HanskiJay
     * @since  2021-04-30
     * @param  boolean      $condition
     * @return void
     */
    public static function getRuntimeDiv(bool $condition = true) : void
    {
        if($condition) {
            echo str_replace('{runTime}', (string) System::getRunTime(), base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB6LWluZGV4OiA5OTk5OTk7IGJvdHRvbTogMDsgcmlnaHQ6IDA7IG1hcmdpbjogNXB4OyBwYWRkaW5nOiA1cHg7IGJhY2tncm91bmQtY29sb3I6ICNhYWFhYWE7IGJvcmRlci1yYWRpdXM6IDVweDsiPgoJPGRpdj5Vc2VkVGltZTogPGI+e3J1blRpbWV9czwvYj48L2Rpdj4KPC9kaXY+'));
        }
    }
}