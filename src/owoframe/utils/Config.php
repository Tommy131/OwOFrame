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
	
************************************************************************/

declare(strict_types=1);
namespace owoframe\utils;

class Config
{
	/* @array 配置文件数组 */
	private $config;
	/* @array 配置文件嵌套缓存数组 */
	private $nestedCache;
	/* @string 文件名 */
	private $fileName;
	/* @string 文件路径 */
	private $filePath;
	/* @bool 自动保存 */
	public $autoSave;


	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		$this->autoSave = $autoSave;
		$this->filePath = dirname($file) . DIRECTORY_SEPARATOR;
		$this->fileName = str_replace($this->filePath, '', $file) . '.json';
		if(!file_exists($file)) {
			$this->config = $defaultData;
			$this->save();
		} else {
			$this->config = json_decode(file_get_contents($this->file), true) ?? [];
		}
	}

	/**
	 * @method      get
	 * @description 获取配置文件项目
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string      $index   键值
	 * @param       mixed       $default 默认返回值
	 * @return      mixed
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
			return $this->config[$index] ?? null;
		}
	}

	/**
	 * @method      getAll
	 * @description 返回配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      array
	 */
	public function getAll() : array
	{
		return $this->config;
	}

	/**
	 * @method      set
	 * @description 向对象设置属性
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string      $index 键值
	 * @param       mixed       $value 数据
	 */
	public function set(string $index, $value) : void
	{
		$arr = explode('.', $index);
		if(count($arr) > 1) {
			$this->config[(string) $arr[0]] = $arr[1];
		} else {
			$this->config[$index] = $value;
		}
		if($this->autoSave) $this->save();
	}

	/**
	 * @method      setAll
	 * @description 向对象设置属性
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       array      $data 键值
	 */
	public function setAll(array $data) : void
	{
		$this->config = $data;
		if($this->autoSave) $this->save();
	}

	public function remove(string $index) : void
	{
		unset($this->config[$index]);
		if($this->autoSave) $this->save();
	}

	/**
	 * @method      exists
	 * @description 判断键值是否存在
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string      $index 键值
	 * @return      boolean
	 */
	public function exists(string $index) : bool
	{
		return isset($this->config[$index]);
	}

	/**
	 * @method      backup
	 * @description 备份配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string      $backupPath 备份路径
	 * @return      void
	 */
	public function backup(string $backupPath = '') : void
	{
		$backupPath = strlen($backupPath === 0) ? $this->filePath : dirname($backupPath);
		$this->save($backupPath . @array_shift(explode('.', $this->fileName)) . '_' . date('Y_m_d') . '.json');
	}

	/**
	 * @method      save
	 * @description 保存配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string|null      $file 文件
	 * @return      void
	 */
	public function save(?string $file = null) : void
	{
		if($file !== null) {
			$filePath = dirname($file) . DIRECTORY_SEPARATOR;
			$fileName = str_replace($filePath, '', $file);
			if($filePath . $fileName !== $this->filePath . $this->fileName) {
				$this->filePath = $filePath;
				$this->fileName = $fileName;
			}
		}
		file_put_contents($file ?? $this->filePath . $this->fileName, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * @method      reload
	 * @description 重新读取配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      void
	 */
	public function reload() : void
	{
		if(is_file($this->filePath . $this->fileName)) {
			$this->nestedCache = [];
			$this->config = json_decode(file_get_contents($this->filePath . $this->fileName, true));
		} else {
			\OwOBootStrap\logger("Cannot reload Config::{$this->config} because the file does not exists!", 'Config', 'ERROR');
		}
	}

	/**
	 * @method      json
	 * @description 将当前的数据转换成JSON对象 | Formating currently data($this->config) to JSON Object
	 * @author      HanskiJay
	 * @doenIn      2021-01-31
	 * @return      object
	 */
	public function json() : object
	{
		return json_decode(json_encode($this->config));
	}
}
