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

use ReflectionClass;
use ReflectionMethod;

use owoframe\exception\ClassMissedException;
use owoframe\exception\InvalidEventException;
use owoframe\exception\OwOFrameException;
use owoframe\exception\ParameterErrorException;

class EventManager implements \owoframe\contract\Manager
{
	/* @int 监听优先级别: 最高 */
	public const HIGHEST_PRIORITY = 5;
	/* @int 监听优先级别: 中等 */
	public const MEDIUM_PRIORITY  = 4;
	/* @int 监听优先级别: 正常 */
	public const NORMAL_PRIORITY  = 3;
	/* @int 监听优先级别: 较低 */
	public const LOW_PRIORITY     = 2;
	/* @int 监听优先级别: 最低 */
	public const LOWEST_PRIORITY  = 1;

	/* @array 事件列表 */
	private $eventList = [];
	/* @array 监听者列表 */
	private $listenerList = [];



	/**
	 * @method      trigger
	 * @description 触发事件, 从而启动监听回调
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @param       string|object@Event      $eventClass 事件名称
	 * @param       array                    $invokeArgs 调用参数传递
	 * @return      mixed
	 */
	public function trigger($eventClass, array $invokeArgs = [])
	{
		if($eventClass instanceof Event) {
			$eventClass = get_class($eventClass);
		}
		if(!is_string($eventClass)) {
			throw new ParameterErrorException('eventClass', 'string or object@Event', $eventClass);
		}

		if(!self::isEvent($eventClass)) {
			if(defined('DEBUG_MODE') && DEBUG_MODE) {
				throw new OwOFrameException('[Event_Register_Failed] Call in unknown event ' . $eventClass);
			}
			return;
		}
		if(!isset($this->eventList[$eventClass])) {
			return;
		}

		krsort($this->eventList[$eventClass]);
		foreach($this->eventList[$eventClass] as $priority => $registerTags) {
			shuffle($registerTags);
			foreach($registerTags as $key => $registerTag) {
				if($this->hasListener($registerTag)) {
					$event = new $eventClass(...$invokeArgs);
					if(($event instanceof Cancellable) && $event->isCancelled()) {
						continue;
					}
					call_user_func_array($this->listenerList[$registerTag]['callback'], [$event]);
					if(!$event->isCalled() && method_exists($event, 'defaultCall')) {
						$event->defaultCall();
					}
				} else {
					unset($this->eventList[$eventClass][$priority][$key]);
				}
			}
		}
	}

	/**
	 * @method      registerListener
	 * @description 注册监听回调到事件
	 * @author      HanskiJay
	 * @doenIn      2021-04-06
	 * @param       string           $registerTag  注册识别标签
	 * @param       mixed            $listener     监听者(类型可为 callableArray, callback, object)
	 * @param       integer          $priority     监听优先级别
	 * @param       boolean          $reload       允许重新注册
	 * @return      void
	 */
	public function registerListener(string $registerTag, $listener, int $priority = self::NORMAL_PRIORITY, bool $reload = false) : void
	{
		if($this->hasListener($registerTag) && !$reload) {
			throw new OwOFrameException("This register tag '{$registerTag}' is already used in a Listener!");
		}
		switch(gettype($listener)) {
			default:
				throw new ParameterErrorException('listener', 'callableArray|callback|object', $listener);
			break;

			case 'object':
				$reflect = new ReflectionClass($listener);
				$methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
				if(count($methods) > 0) {
					foreach($methods as $method) {
						$this->setCallbackToEvent($registerTag, $priority, [$listener, $method->getName()], $reload);
					}
				} else {
					throw new OwOFrameException('Cannot register this object as a listener because it has\'nt valid callable method!');
				}
			break;

			case 'array':
				if(!is_callable($listener)) {
					throw new OwOFrameException('Cloud not callback with this listener!');
				}
				$this->setCallbackToEvent($registerTag, $priority, $listener, $reload);
			break;
		}
	}

	/**
	 * @method      unregisterListener
	 * @description 注销监听器
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @param       string             $registerTag 注册识别标签
	 * @return      void
	 */
	public function unregisterListener(string $registerTag) : void
	{
		if(!$this->hasListener($registerTag)) {
			return;
		}
		$eventClass = $this->listenerList[$registerTag]['event'];
		$priority   = $this->listenerList[$registerTag]['priority'];
		$key        = array_search($registerTag, $this->eventList[$eventClass][$priority]);
		if($key !== false) {
			unset($this->eventList[$eventClass][$priority][$key]);
		}
		unset($eventClass, $priority, $key, $this->listenerList[$registerTag]);
	}

	/**
	 * @method      setCallbackToEvent
	 * @description 将监听者分配到对应的事件
	 * @author      HanskiJay
	 * @doenIn      2021-04-09
	 * @param       string      $registerTag  注册识别标签
	 * @param       callable    $callback     回调
	 * @param       integer     $priority     监听优先级别
	 * @param       boolean     $reload       允许重新注册
	 * @return      void
	 */
	public function setCallbackToEvent(string $registerTag, int $priority, callable $callback, bool $reload = false) : void
	{
		if(($priority > 5) || ($priority <= 0)) {
			throw new OwOFrameException("[Event_Priority_Invalid] The priority of register tag '{$registerTag}' should between 1 ~ 6!");
		}
		if($this->hasListener($registerTag) && !$reload) {
			return;
		}

		$reflect   = new ReflectionMethod($callback[0], $callback[1]);
		$parameter = @array_shift($reflect->getParameters());

		if(($parameter !== null) && (($parameter = $parameter->getType()) !== null)) {
			if(!self::isEvent($eventClass = $parameter->getName())) {
				return;
				// throw new OwOFrameException('[Event_Register_Failed] Call in unknown event ' . $eventClass);
			}

			$this->listenerList[$registerTag] = ['priority' => $priority, 'callback' => $callback, 'event' => $eventClass];
			$this->eventList[$eventClass][$priority][] = $registerTag;
		}/* else {
			throw new OwOFrameException('Cannot register this callback as a listener because the callback method is invalid!');
		}*/
	}

	/**
	 * @method      hasListener
	 * @description 判断是否存在监听器
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @param       string      $registerTag 注册识别标签
	 * @return      boolean
	 */
	public function hasListener(string $registerTag) : bool
	{
		return isset($this->listenerList[$registerTag]);
	}

	/**
	 * @method      isEvent
	 * @description 判断是否为标准事件类
	 * @author      HanskiJay
	 * @doenIn      2021-04-10
	 * @param       string      $eventClass 事件名称
	 * @return      boolean
	 */
	public static function isEvent(string $eventClass) : bool
	{
		return !class_exists($eventClass) ? false : is_a($eventClass, Event::class, true);
	}
}