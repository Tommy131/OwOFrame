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
namespace owoframe;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Reflector;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionException;
use owoframe\exception\OwOFrameException;

class Container implements ArrayAccess, Countable
{

	/* @Container 容器实例 */
	protected static $instance = null;
	/* @array 容器绑定标识 */
	protected $bind = [];
	/* @array 对象实例列表 */
	protected $instances = [];


	/**
	 * @method      getInstance
	 * @description 返回容器单例实例
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @return      @Container
	 */
	public static function getInstance() : Container
	{
		if(!static::$instance instanceof Container) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * @method      bind
	 * @description 绑定到容器绑定标识
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string      $bindTag  绑定标识
	 * @param       mixed       $concrete 参数可为[类名|对象|闭包]
	 * @return      void
	 */
	public function bind(string $bindTag, $concrete) : void
	{
		if(is_string($concrete) && class_exists($concrete)) {
			$this->bind[$bindTag] = $concrete;
		}
		elseif(is_object($concrete)) {
			$this->instance($bindTag, $concrete);
		}
	}

	/**
	 * @method      instance
	 * @description 绑定实例到对象实例列表
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string      $bindTag  绑定标识
	 * @param       object      $instance 实例化对象
	 * @return      void
	 */
	public function instance(string $bindTag, $instance) : void
	{
		if(is_object($instance) && (!$instance instanceof Closure)) {
			$this->instances[$bindTag] = $instance;
		}
	}

	/**
	 * @method      make
	 * @description 创建实例(存在即返回或自动更新)
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string       $bindTag    绑定标识
	 * @param       array        $params     参数
	 * @param       bool|boolean $autoUpdate 自动更新实例开关
	 * @return      object
	 */
	public function make(string $bindTag, array $params = [], bool $autoUpdate = false)
	{
		if(!$this->offsetExists($bindTag)) {
			return new OwOFrameException('No bindTag found');
		}

		if($this->has($bindTag, 1) && !$autoUpdate) {
			return $this->instances[$bindTag];
		}

		$object = $this->invoke($this->has($bindTag) && ($this->bind[$bindTag] instanceof Closure) ? 'fn' : 'cls', $this->bind[$bindTag], $params);
		if($autoUpdate) {
			$this->instance($bindTag, $object);
		}
		return $object;
	}

	/**
	 * @method      invoke
	 * @description 调用选择器(function|class|Closure|Method)
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string      $selector
	 * @param       mixed       $concrete
	 * @param       array       $params
	 * @return      
	 */
	public function invoke(string $selector, $concrete, array $params = [])
	{
		$selector = strtolower($selector);
		try {
			switch($selector) {
				case 'fn':
				case 'function':
					$reflect = new ReflectionFunction($concrete);
					if($reflect->getNumberOfParameters() > 0) {
						$params = $this->bindParams($reflect, $params);
					}
					$object = $concrete(...$params);
				break;

				case 'cls':
				case 'class':
					$reflect = new ReflectionClass($concrete);
					$constructor = $reflect->getConstructor();
					$object  = $reflect->newInstanceArgs($constructor ? $this->bindParams($constructor, $params) : []);
				break;
			}
			return $object;
		} catch(ReflectionException $e) {
			throw new OwOFrameException("[invoke({$selector})]: {$name} not exists", 0, $e);
		}
	}

	/**
	 * @method      invokeReflectMethod
	 * @description 调用反射执行类的方法
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @param       Reflector    $reflect [description]
	 * @param       array        $params  [description]
	 * @return      [type]                [description]
	 */
	public function invokeReflectMethod(Reflector $reflect, array $params = [])
	{
		return method_exists($reflect, 'invokeArgs') ? $reflect->invokeArgs($this->bindParams($reflect, $params)) : null;
	}

	/**
	 * @method      bindParams
	 * @description 绑定参数
	 * @author      HanskiJay
	 * @doenIn      2021-03-06
	 * @param       ReflectionFunctionAbstract $reflect 反射类实例
	 * @param       array                      $params  参数组
	 * @return      array
	 */
	public function bindParams(ReflectionFunctionAbstract $reflect, array $params = []) : array
	{
		if(($count = $reflect->getNumberOfParameters()) === 0) {
			return [];
		}
		$newParams = [];
		foreach($reflect->getParameters() as $key => $reflectParam) {
			$param = isset($params[$key]) ? $params[$key] : 
					(isset($params[$reflectParam->getName()]) ? $params[$reflectParam->getName()] : 
						($reflectParam->isDefaultValueAvailable() ? $reflectParam->getDefaultValue() : null) // ←END HERE;
					);
			if(($paramType = $reflectParam->getType()) !== null) {
				$paramType = $paramType->getName();
				// compatibility function@gettype;
				switch($paramType) {
					case 'int':
						$paramType = 'integer';
					break;

					case 'float':
						$paramType = 'double';
					break;
				}
				if(gettype($param) === $paramType) {
					$newParams[] = $param;
				}
			} else {
				$newParams[] = $param;
			}
		}
		if(count($newParams) !== $count) {
			throw new OwOFrameException("Function {$reflect->getName()} expected {$count} parameter(s), " . count($newParams) . " is given.");
		}
		return $newParams;
	}

	/**
	 * @method      has
	 * @description 判断容器绑定标识或对象实例列表中是否存在一个绑定标识
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string      $bindTag    绑定标识
	 * @param       int|integer $selectMode 选择模式
	 * @return      boolean
	 */
	public function has(string $bindTag, int $selectMode = 0) : bool
	{
		// 0:  Select to [$this->bind] mode;
		// !0: Select to [$this->instances] mode;
		return ($selectMode === 0) ? isset($this->bind[$bindTag]) : isset($this->instances[$bindTag]);
	}

	/**
	 * @method      count
	 * @description 返回所有已实例化的对象
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @return      integer
	 */
	public function count() : int
	{
		return count($this->instances);
	}

	/**
	 * @method      getIterator
	 * @description 返回数组迭代器实例
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @return      @ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->instances);
	}

	/**
	 * @method      offsetExists
	 * @description @ArrayAccess
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       mixed $key
	 * @return      mixed
	 */
	public function offsetExists($key)
	{
		return $this->has($key) || $this->has($key, 1);
	}

	/**
	 * @method      offsetGet
	 * @description @ArrayAccess
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       mixed $key
	 * @return      mixed
	 */
	public function offsetGet($key)
	{
		return $this->make($key);
	}

	/**
	 * @method      offsetSet
	 * @description @ArrayAccess
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       mixed $key
	 */
	public function offsetSet($key, $val)
	{
		$this->bind($key, $val);
	}

	/**
	 * @method      offsetUnset
	 * @description @ArrayAccess
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       mixed $key
	 */
	public function offsetUnset($key)
	{
		if($this->has($key)) {
			unset($this->bind[$key]);
		}
		if($this->has($key, 1)) {
			unset($this->instances[$key]);
		}
	}
}