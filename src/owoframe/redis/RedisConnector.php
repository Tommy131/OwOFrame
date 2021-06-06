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
namespace owoframe\redis;

use Redis;
use owoframe\exception\MethodMissedException;
use owoframe\object\Config;

class RedisConnector
{
	/* class@RedisConnector RedisConnector实例 */
	protected static $instance = null;
	/* class@Redis Redis实例 */
	protected $handler = null;
	/* @array 配置文件 */
	protected $config =
	[
		'host'       => '127.0.0.1',
		'port'       => 6379,
		'password'   => '',
		'select'     => 0,
		'timeout'    => 5,
		'persistent' => false,
		'prefix'     => '',
	];
	/* @string Redis容器名称 */
	protected $name = '';

	/**
	 * @method      getConnection
	 * @description 新建Redis连接
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @return      null or object@Redis
	 */
	public function getConnection() : ?Redis
	{
		if($this->isAlive()) {
			return $this->handler;
		}

		$this->handler = new Redis;
		if($this->cfg('persistent')) {
			$this->handler->pconnect($this->cfg('host'), $this->cfg('port'), $this->cfg('timeout'), 'persistent_id_' . $this->cfg('select'));
		} else {
			if(!$this->handler->connect($this->cfg('host'), $this->cfg('port'), $this->cfg('timeout'))) {
				$this->handler = null;
			} else {
				if(!empty($this->cfg('password'))) {
					$this->handler->auth($this->cfg('password'));
				}
			}
		}
		return $this->handler;
	}

	/**
	 * @method      forceUsePassword
	 * @description 使用强制密码访问模式
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @param       bool[mode|强制使用密码认证]
	 * @return      void
	 */
	public function forceUsePassword(bool $mode = true) : void
	{
		if(!$this->isAlive()) return;
		if($mode) {
			$this->handler->config('SET', 'requirepass', $this->cfg('password'));
			$this->handler->auth($this->cfg('password'));
		} else {
			$this->cfg('password', '', true);
			$this->handler->config('SET', 'requirepass', $this->cfg('password'));
		}
	}

	/**
	 * @method      cfg
	 * @description 返回配置文件的项目
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @param       string[index|键名]
	 * @param       mixed[val|值]
	 * @param       bool[update|更新配置文件项目]
	 * @return      mixed
	 */
	public function cfg(string $index, $val = '', bool $update = false)
	{
		$index = strtolower($index);
		if($update) {
			if($index === 'host') {
				$split = explode(':', $val);
				if(count($split) === 2) {
					$this->config['host'] = array_shift($split);
					$this->config['port'] = (int) array_shift($split);
				} else {
					$this->config['host'] = array_shift($split);
					$this->config['port'] = 5300;
				}
			} else {
				$this->config[$index] = $val;
			}
		} else {
			if(isset($this->config[$index])) {
				switch($index) {
					default:
						$proxy = $this->config[$index];
					break;

					case 'port':
					case 'select':
						$proxy = (int) $this->config[$index];
					break;

					case 'timeout':
						$proxy = (float) $this->config[$index];
					break;

					case 'persistent':
						$proxy = (bool) $this->config[$index];
					break;

				}
			}
		}
		return $proxy ?? $val;
	}

	/**
	 * @method      saveCfg
	 * @description 保存当前设置的Redis配置文件
	 * @author      HanskiJay
	 * @doneIn      2021-04-17
	 * @return      [type]      [description]
	 */
	public function saveCfg() : void
	{
		$cfg = new Config(FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'redis.json');
		$cfg->set($this->name ?? spl_object_hash($this), $this->cfg);
		$cfg->save();
	}

	/**
	 * @method      isAlive
	 * @description 判断当前连接是否有效
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @return      boolean
	 */
	public function isAlive() : bool
	{
		return $this->handler instanceof Redis;
	}

	/**
	 * @method      getInstance
	 * @description 返回实例化对象
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @return      class@Redis
	 */
	public static function getInstance() : RedisConnector
	{
		if(!static::$instance instanceof RedisConnector) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * @method      getHandler
	 * @description 返回Redis实例化对象(若未正确配置则返回null)
	 * @author      HanskiJay
	 * @doneIn      2021-02-14
	 * @return      null or class@Redis
	 */
	public function getHandler() : ?Redis
	{
		if(!$this->handler instanceof Redis) {
			$this->handler = null;
		}
		return $this->handler;
	}

	/**
	 * @method      getName
	 * @description 返回当前Redis容器名称
	 * @author      HanskiJay
	 * @doneIn      2021-04-17
	 * @return      string
	 */
	public function &getName() : string
	{
		return $this->name;
	}

	/**
	 * @method      __call
	 * @description 使用PHP的魔术方法回调Redis类中的方法
	 * @author      HanskiJay
	 * @doneIn      2021-02-16
	 * @return      mixed
	 */
	public function __call($name, $args)
	{
		if(($this->isAlive()) && method_exists($this->handler, $name)) {
			return $this->handler->{$name}(...$args);
		} else {
			throw new MethodMissedException(get_class($this), $name);
		}
	}

	/**
	 * @method      __construct
	 * @access      private
	 * @description 阻止外部调用者实例化此对象
	 * @author      HanskiJay
	 * @doneIn      2021-02-16
	 */
	private function __construct() {}
}