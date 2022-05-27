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
namespace owoframe\object;

use owoframe\utils\Logger;

abstract class Config
{
	/**
	 * 配置文件数组
	 *
	 * @access protected
	 * @var array
	 */
	protected $config;

	/**
	 * 配置文件嵌套缓存数组
	 *
	 * @access protected
	 * @var array
	 */
	protected $nestedCache;

	/**
	 * 文件名
	 *
	 * @access protected
	 * @var string
	 */
	protected $fileName;

	/**
	 * 文件路径
	 *
	 * @access protected
	 * @var string
	 */
	protected $filePath;

	/**
	 * 自动保存
	 *
	 * @var bool
	 */
	public $autoSave;


	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		$this->autoSave = $autoSave;
		$this->filePath = dirname($file) . DIRECTORY_SEPARATOR;
		if(!is_dir($this->filePath)) {
			mkdir($this->filePath, 755, true);
		}
		$fileName       = explode('.', basename($file)); // e.g. abc.json | abc;
		$fileName       = array_shift($fileName);  // if yes, then shift 'abc' to $file;
		$this->fileName = str_replace($this->filePath, '', $fileName);


		if(!file_exists($file) || (filesize($file) === 0)) {
			$this->config = $defaultData;
			$this->save();
		} else {
			$this->reload();
		}
	}

	/**
	 * 获取配置文件项目
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string $index   键值
	 * @param  mixed  $default 默认返回值
	 * @return mixed
	 */
	public function get(string $index, $default = null)
	{
		$arr = explode('.', $index);
		if(count($arr) > 1) {
			if(isset($this->nestedCache[$index])) return $this->nestedCache[$index];

			$base = array_shift($arr);
			if(isset($this->config[$base])) {
				$base = $this->config[$base];
			} else {
				return $default;
			}

			while(count($arr) > 0) {
				$baseKey = array_shift($arr);
				if(is_array($base) && isset($base[$baseKey])) {
					$base = $base[$baseKey];
				} else {
					return $default;
				}
			}
			return $this->nestedCache[$index] = $base;
		} else {
			return $this->config[$index] ?? $default;
		}
	}

	/**
	 * 向对象设置属性
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string $index 键值
	 * @param  mixed  $value 数据
	 * @return void
	 */
	public function set(string $index, $value) : void
	{
		$arr = explode('.', $index);
		if(count($arr) > 1) {
			$base = array_shift($arr);
			if(!isset($this->config[$base])) {
				$this->config[$base] = [];
			}

			$base =& $this->config[$base];
			if(!is_array($base)) {
				return;
			}

			while(count($arr) > 0) {
				$baseKey = array_shift($arr);
				if(!isset($base[$baseKey])) {
					$base[$baseKey] = [];
				}
				$base =& $base[$baseKey];
			}
			$base = $value;
			$this->nestedCache[$index] = $value;
		} else {
			$this->config[$index] = $value;
		}
		if($this->autoSave) $this->save();
	}

	/**
	 * 向对象设置属性
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  array $data 数据
	 * @return void
	 */
	public function setAll(array $data) : void
	{
		$this->config = $data;
		if($this->autoSave) $this->save();
	}

	/**
	 * 移除变量值
	 *
	 * @author HanskiJay
	 * @since  2021-05-04
	 * @param  string $index 键名
	 * @return void
	 */
	public function remove(string $index) : void
	{
		if($this->exists($index)) {
			unset($this->config[$index]);
			if($this->autoSave) $this->save();
		}
	}

	/**
	 * 保存配置文件
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string|null $file 文件
	 * @return void
	 */
	abstract public function save(?string $file = null) : void;

	/**
	 * 重新读取配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return void
	 */
	abstract protected function reloadCallback() : void;

	/**
	 * 重新读取配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return void
	 */
	public function reload() : void
	{
		if(is_file($this->getFullPath())) {
			$this->reloadCallback();
		} else {
			Logger::getInstance()->error("Cannot reload Config::{$this->getFileName()}, because the file does not exists!");
		}
	}

	/**
	 * 备份配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string $backupPath 备份路径
	 * @return void
	 */
	public function backup(string $backupPath = '') : void
	{
		if(!is_dir($backupPath)) {
			$backupPath = $this->getFilePath();
		}
		$this->save($backupPath . $this->getFileName() . '_' . date('Y_m_d_H_i_s') . $this->getExtensionName());
	}

	/**
	 * 判断键值是否存在
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string $index 键值
	 * @return boolean
	 */
	public function exists(string $index) : bool
	{
		return isset($this->config[$index]);
	}

	/**
	 * 返回配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return array
	 */
	public function getAll() : array
	{
		return $this->config;
	}

	/**
	 * 将当前的数据转换成对象 | Formatting currently data($this->config) to Object
	 *
	 * @author HanskiJay
	 * @since  2021-01-31
	 * @return object
	 */
	public function obj() : object
	{
		return (object) $this->config;
	}

	/**
	 * 返回当前配置文件名称
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string
	 */
	public function getFileName() : string
	{
		return $this->fileName;
	}

	/**
	 * 返回当前配置文件路径
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string
	 */
	public function getFilePath() : string
	{
		return $this->filePath;
	}

	/**
	 * 返回配置文件完整路径
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string
	 */
	public function getFullPath() : string
	{
		return $this->filePath . $this->fileName . $this->getExtensionName();
	}

	/**
	 * 返回配置文件扩展名称
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string
	 */
	abstract public function getExtensionName() : string;
}
