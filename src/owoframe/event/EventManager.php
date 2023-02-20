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
 * @Date         : 2023-02-03 23:51:38
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-20 20:04:41
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\event;



use ReflectionMethod;
use ReflectionException;

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
            throw new ReflectionException("Method {$listener}::{$method} must be public and callable.");
        }

        $parameters = $reflection->getParameters();
        $parameters = array_shift($parameters);
        if(is_null($parameters)) {
            return;
        }

        $eventName  = $parameters->getType()->getName();
        if(!self::isEvent($eventName)) {
            throw new ReflectionException("Attempt to register an none-exists Event ({$eventName})");
        }

        $class = get_class($listener);
        $tag   = $class . '@' . $method;

        // 创建映射关系
        $this->registeredListener[$class][$method] = $eventName;
        // 注册事件
        $this->handlerList[$eventName][$listener::priority()][$class][$tag] = [$listener, $method];
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
        $class = get_class($listener);
        $tag   = $class . '@' . $method;

        if(!$this->hasListener($listener)) {
            return;
        }
        $_ =& $this->registeredListener[$class];

        if(isset($_[$method])) {
            $eventName = $_[$method];
            $__        =& $this->handlerList[$eventName][$listener::priority()][$class];
            unset($__[$tag], $_[$method]);

            // 判断当前监听者是否存在其他的事件回调
            if(empty($_)) {
                unset($_, $__);
            }
        }
    }

    /**
     * 判断是否存在一个监听者
     *
     * @param  Listener|string $listener
     * @return boolean
     */
    public function hasListener($listener) : bool
    {
        $listener = ($listener instanceof Listener) ? get_class($listener) : (is_string($listener) ? $listener : null);
        return isset($this->registeredListener[$listener]);
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
?>