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
namespace owoframe\event;

use owoframe\exception\OwOFrameException;
use ReflectionMethod;

class EventManager
{
    /**
     * 单例实例
     *
     * @var EventManager
     */
    private static $instance = null;

    /**
     * 已注册的监听者列表
     *
     * @var array
     */
    private $registeredListener = [];
    /**
     * 回调列表
     *
     * @var array
     */
    private $handlerList = [];


    private function __construct()
    {
    }

    /**
     * 添加触发器回调
     *
     * @param  Listener $listener
     * @param  string   $method
     * @return void
     */
    public function addTriggerCallback(Listener $listener, string $method) : void
    {
        $reflection = new ReflectionMethod($listener, $method);
        if(!$reflection->isPublic()) {
            throw new OwOFrameException("Method {$listener}::{$method} must be public and callable.");
        }

        $parameters = $reflection->getParameters();
        $parameters = array_shift($parameters);
        if(is_null($parameters)) {
            return;
        }

        $eventName  = $parameters->getType()->getName();
        if(!self::isEvent($eventName)) {
            throw new OwOFrameException("Attempt to register an none-exists Event ({$eventName})");
        }
        $splId   = spl_object_hash($listener);
        $hashTag = $splId . '@' . $method;
        // 创建映射关系;
        $this->registeredListener[$splId][$method] = $eventName;
        // 注册事件;
        $this->handlerList[$eventName][$splId][$hashTag] = [$listener, $method];
    }

    /**
     * 删除触发器回调
     *
     * @param  Listener $listener
     * @param  string   $method
     * @return void
     */
    public function deleteTriggerCallback(Listener $listener, string $method) : void
    {
        $splId   = spl_object_hash($listener);
        $hashTag = $splId . '@' . $method;
        if(isset($this->registeredListener[$splId][$method])) {
            $eventName = $this->registeredListener[$splId][$method];
            unset($this->handlerList[$eventName][$splId][$hashTag], $this->registeredListener[$splId][$method]);
            // 判断当前监听者是否存在事件回调;
            if(empty($this->registeredListener[$splId])) {
                unset($this->registeredListener[$splId], $this->handlerList[$eventName][$splId]);
            }
        }
    }

    /**
     * 返回事件的处理列表
     *
     * @param  Event  $event
     * @return array
     */
    public function getHandlerList(Event $event) : array
    {
        $eventName = $event->getName();
        return $this->handlerList[$eventName] ?? [];
    }

    /**
     * 判断是否为标准事件类
     *
     * @author HanskiJay
     * @since  2021-04-10
     * @param  string  $event 事件名称
     * @return boolean
     */
    public static function isEvent(string $event) : bool
    {
        return !class_exists($event) ? false : is_a($event, Event::class, true);
    }

    /**
     * 返回单例实例
     *
     * @return EventManager
     */
    public static function getInstance() : EventManager
    {
        if(!static::$instance instanceof EventManager) {
            static::$instance = new static;
        }
        return static::$instance;
    }

}