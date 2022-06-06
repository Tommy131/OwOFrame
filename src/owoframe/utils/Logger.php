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
namespace owoframe\utils;

use owoframe\helper\Helper;
use owoframe\object\INI;
use owoframe\utils\TextFormat;

class Logger implements \owoframe\interfaces\Unit
{

	/**
	 * 默认绑定标签
	 */
	public const DEFAULT_BIND_TAG = 'main';

	/**
	 * 默认配置文件
	 */
	public const DEFAULT_CONFIG =
	[
		'fileName'    => 'owoblog_run.log',      // 默认日志文件名称;
		'logFormat'   => '[%s][%s][%s/%s] > %s', // 日志记录格式;
		'maximumSize' => '1024',                 // 最大文件大小, MB;
		'logPrefix'   => 'OwO',                  // 日志记录前缀;
	];

	/**
	 * 自身实例对象
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * 当前绑定的日志记录标签
	 *
	 * @var string
	 */
	private $selected;

	/**
	 * 已占用的日志记录绑定标签
	 *
	 * @var array
	 */
	private $usedBindTags = [];


	/**
	 * 构造函数
	 */
	public function __construct()
	{
		if(!static::$instance instanceof Logger) {
			static::$instance = $this;
		}
		$this->createLogger(self::DEFAULT_BIND_TAG, [], true);
	}

	/**
	 * 检测是否存在一个日志记录容器
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @param  string  $bindTag
	 * @return boolean
	 */
	public function hasLogger(string $bindTag) : bool
	{
		return isset($this->usedBindTags[$bindTag]);
	}

	/**
	 * 返回当前选择的日志记录容器标签;
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @return string
	 */
	public function getCurrentLogger() : string
	{
		return $this->selected;
	}

	/**
	 * 选择日志记录容器
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @param  string  $bindTag
	 * @return Logger
	 */
	public function selectLogger(string $bindTag) : Logger
	{
		$this->selected = (!$this->hasLogger($bindTag)) ? self::DEFAULT_BIND_TAG : $bindTag;
		return $this;
	}

	/**
	 * 创建一个日志记录容器
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @param  string  $bindTag
	 * @param  array   $config
	 * @param  boolean $autoSelect
	 * @return Logger
	 */
	public function createLogger(string $bindTag, array $config = [], bool $autoSelect = true) : Logger
	{
		if(!$this->hasLogger($bindTag)) {
			$this->usedBindTags[$bindTag] = checkArrayValid($config, self::DEFAULT_CONFIG) ? $config : self::DEFAULT_CONFIG;
		}
		if($autoSelect) {
			$this->selectLogger($bindTag);
		}
		return $this;
	}

	/**
	 * 返回一个日志记录容器的配置文件
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @param  string      $bindTag
	 * @return object|null
	 */
	public function getConfig(string $bindTag) : ?object
	{
		if(!$this->hasLogger($bindTag)) {
			return null;
		}
		return (object) $this->usedBindTags[$bindTag];
	}

	/**
	 * 更新配置文件
	 *
	 * @author HanskiJay
	 * @since  2022-05-08
	 * @param  string  $bindTag
	 * @param  [type]  $var
	 * @param  string  $val
	 * @return boolean
	 */
	public function updateConfig(string $bindTag, $var, string $val = '') : bool
	{
		if(!$this->hasLogger($bindTag)) {
			return false;
		}
		if(is_array($var)) {
			$this->usedBindTags[$bindTag] = array_merge($this->usedBindTags[$bindTag], $var);
		}
		elseif(is_string($var)) {
			$this->usedBindTags[$bindTag][$var] = $val;
		} else {
			return false;
		}
		return true;
	}

	/**
	 * 写入日志
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $level   日志等级
	 * @return void
	 */
	public function write(string $message, string $level, string $color = TextFormat::WHITE) : void
	{
		// Get currently Logger's configuration;
		$config = $this->getConfig($this->selected);

		// Check currently log file size;
		if(is_file($config->fileName) && (filesize($config->fileName) >= $config->maximumSize * 1000)) {
			rename($config->fileName, str_replace('.log', '', $config->fileName) . date('_Y_m_d') . '.log');
		}

		// Format output message;
		$message = $color . sprintf($config->logFormat, date('Y-m-d'), date('H:i:s'), $config->logPrefix, strtoupper($level), $message) . PHP_EOL;

		if(Helper::isRunningWithCLI()) {
			echo TextFormat::parse($message);
		}
		if(INI::_global('owo.enableLog', true)) {
			file_put_contents(LOG_PATH . $config->fileName, TextFormat::clean($message), FILE_APPEND | LOCK_EX);
		}
	}


	/**
	 * 日志写入: INFO 等级 (仅颜色显示不同)
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function success(string $message, string $color = TextFormat::GREEN) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: INFO 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function info(string $message, string $color = TextFormat::WHITE) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: NOTICE 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function notice(string $message, string $color = TextFormat::AQUA) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: WARNING 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function warning(string $message, string $color = TextFormat::GOLD) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: ERROR 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function error(string $message, string $color = TextFormat::RED) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: EMERGENCY 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function emergency(string $message, string $color = TextFormat::LIGHT_RED) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 日志写入: DEBUG 等级
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $message 日志内容
	 * @param  string      $color   默认输出颜色 (仅在CLI模式下)
	 * @return void
	 */
	public function debug(string $message, string $color = TextFormat::GRAY) : void
	{
		$this->write($message, __FUNCTION__, $color);
	}

	/**
	 * 发送空行到CLI
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @return void
	 */
	public static function sendEmpty() : void
	{
		echo PHP_EOL;
	}

	/**
	 * 删除错误日志
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string      $fileName 日志名称
	 * @return void
	 */
	public static function cleanLog(string $fileName = '') : void
	{
		if(is_null($fileName)) $fileName = LOG_PATH . self::DEFAULT_CONFIG['fileName'];
		if(is_file($fileName)) unlink($fileName);
	}

	/**
	 * 返回自身实例
	 *
	 * @author HanskiJay
	 * @since  2022-05-15
	 * @return Logger
	 */
	public static function getInstance() : Logger
	{
		if(!static::$instance instanceof Logger) {
			new static;
		}
		return static::$instance;
	}
}
?>