<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	* 本类Session数据的存储格式采用json数据格式进行封装, 并且使用base64进行编码;
	* !注意: 使用此方法需要自行使用session_start();
************************************************************************/

namespace backend\system\redis;

use Closure;
use Redis;
use backend\OwOFrame;
use backend\system\exception\{OwOFrameException, MethodMissedException};

class RedisConnector
{
	/* class@RedisConnector RedisConnector实例 */
	private static $instance = null;
	/* class@Redis Redis实例 */
	private $handler = null;
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

	/**
	 * @method      getConnection
	 * @description 新建Redis连接
	 * @author      HanskiJay
	 * @doenIn      2021-02-14
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
	 * @method      isAlive
	 * @description 判断当前连接是否有效
	 * @author      HanskiJay
	 * @doenIn      2021-02-14
	 * @return      boolean
	 */
	public function isAlive() : bool
	{
		return $this->handler instanceof Redis;
	}

	/**
	 * @method      forceUsePassword
	 * @description 使用强制密码访问模式
	 * @author      HanskiJay
	 * @doenIn      2021-02-14
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
	 * @doenIn      2021-02-14
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
					$this->config['port'] = 6379;
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
	 * @method      getInstance
	 * @description 返回实例化对象
	 * @author      HanskiJay
	 * @doenIn      2021-02-14
	 * @return      class@Redis
	 */
	public static function getInstance() : RedisConnector
	{
		if(!self::$instance instanceof RedisConnector) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __call($name, $args)
	{
		if(($this->isAlive()) && method_exists($this->handler, $name)) {
			return $this->handler->{$name}(...$args);
		} else {
			throw new MethodMissedException(get_class($self), $name);
		}
	}
}