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
 * @Date         : 2023-02-20 03:05:15
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-20 06:18:41
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\object;



/**
 * 使用责任链模式（Chain of Responsibility Pattern）在管道中的连续调用
 */
class Pipe
{
    /**
     * 主回调
     *
     * @var callable
     */
    protected $mainCallback = null;

    /**
     * 结果检查回调
     *
     * @var callable
     */
    protected $resultCheckerCallback = null;

    /**
     * 是否继续询问
     *
     * @var boolean
     */
    protected $continue = true;

    /**
     * 返回上一次执行结果
     *
     * @var boolean|null
     */
    protected $lastResult = null;

    /**
     * 返回最终执行结果
     *
     * @var boolean|null
     */
    protected $finalResult = null;

    /**
     * 当前期望返回的结果
     *
     * @var mixed
     */
    protected $expectingResult = null;


    /**
     * 构造函数
     *
     * @param  callable|null $mainCallback
     * @param  callable|null $resultCheckerCallback
     */
    public function __construct(?callable $mainCallback = null, ?callable $resultCheckerCallback = null)
    {
        $this->mainCallback          = $mainCallback;
        $this->resultCheckerCallback = $resultCheckerCallback;
    }

    /**
     * 下一步操作
     *
     * @return Pipe
     */
    public function then() : Pipe
    {
        if($this->continue) {
            $mainCallback          = $this->mainCallback;
            $resultCheckerCallback = $this->resultCheckerCallback;
            $args                  = func_get_args();
            $this->lastResult      = $mainCallback(...$args);

            // 将执行结果传入结果检查回调
            if(!$resultCheckerCallback($this->lastResult)) {
                $this->lastResult = null;
            }
        }

        if(!$this->hasLastResult()) {
            $this->setContinue(false);
        }
        return $this;
    }

    /**
     * 立刻执行此操作, 不检测
     *
     * @return Pipe
     */
    public function do(callable $callback) : Pipe
    {
        $this->lastResult = $callback($this);
        return $this;
    }

    /**
     * 最终执行方法
     *
     * @param  callable $callback
     * @return Pipe
     */
    public function finally(callable $callback) : Pipe
    {
        if($this->isContinue()) {
            $this->finalResult = $callback($this);
        } else {
            $this->lastResult = null;
        }
        return $this;
    }

    /**
     * 设置连续询问
     *
     * @param  boolean $_
     * @return Pipe
     */
    public function setContinue(bool $_) : Pipe
    {
        $this->continue = $_;
        return $this;
    }

    /**
     * 检测是否允许继续执行
     *
     * @return boolean
     */
    public function isContinue() : bool
    {
        return $this->continue;
    }

    /**
     * 设置期望返回的结果
     *
     * @param  mixed $expecting
     * @return Pipe
     */
    public function setExpectingResult($expecting) : Pipe
    {
        $this->expectingResult = $expecting;
        return $this;
    }

    /**
     * 设置期望返回的结果
     *
     * @param  array $expecting
     * @return Pipe
     */
    public function setExpectingResults(array $expecting) : Pipe
    {
        $this->expectingResult = $expecting;
        return $this;
    }

    /**
     * 返回期望的结果
     *
     * @return mixed
     */
    public function getExpectingResult()
    {
        return $this->expectingResult;
    }

    /**
     * 判断上次执行结果的值是否有效
     *
     * @return boolean
     */
    public function hasLastResult() : bool
    {
        return (($this->lastResult === null) ? false : (($this->lastResult === '')  ? false : true));
    }

    /**
     * 返回上次执行结果的值
     *
     * @return mixed
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * 返回最后执行结果的值
     *
     * @return mixed
     */
    public function getFinalResult()
    {
        return $this->finalResult;
    }

    /**
     * 设置主回调
     *
     * @param  callable $callback
     * @return Pipe
     */
    public function setMainCallback(callable $callback) : Pipe
    {
        $this->mainCallback = $callback;
        return $this;
    }

    /**
     * 设置结果检查回调
     *
     * @param  callable $callback
     * @return Pipe
     */
    public function setResultCheckerCallback(callable $callback) : Pipe
    {
        $this->resultCheckerCallback = $callback;
        return $this;
    }

    /**
     * 返回主回调
     *
     * @return callable
     */
    public function getMainCallback() : callable
    {
        return $this->mainCallback;
    }

    /**
     * 返回结果检查回调
     *
     * @return callable
     */
    public function getResultCheckerCallback() : callable
    {
        return $this->resultCheckerCallback;
    }
}
?>