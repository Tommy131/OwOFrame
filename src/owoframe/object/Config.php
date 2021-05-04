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
	* GitHub: https://github.com/Tommy131
	
************************************************************************/

declare(strict_types=1);
namespace owoframe\object;

abstract class Config
{
	/* @array 配置文件数组 */
	protected $config;
	/* @array 配置文件嵌套缓存数组 */
	protected $nestedCache;
	/* @string 文件名 */
	protected $fileName;
	/* @string 文件路径 */
	protected $filePath;
	/* @bool 自动保存 */
	public $autoSave;


	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		$this->autoSave = $autoSave;
		$this->filePath = dirname($file) . DIRECTORY_SEPARATOR;
		if(!is_dir($this->filePath)) {
			mkdir($this->filePath, 755, true);
		}
		$fileName       = explode('.', $file); // e.g. abc.json | abc;
		$fileName       = array_shift($fileName);  // if yes, then shift 'abc' to $file;
		$this->fileName = str_replace($this->filePath, '', $fileName);
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
			return $this->config[$index] ?? $default;
		}
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
			$base = array_shift($arr);
			if(!isset($this->config[$base])){
				$this->config[$base] = [];
			}

			$base =& $this->config[$base];

			while(count($arr) > 0){
				$baseKey = array_shift($arr);
				if(!isset($base[$baseKey])){
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
	 * @method      setAll
	 * @description 向对象设置属性
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       array      $data 数据
	 */
	public function setAll(array $data) : void
	{
		$this->config = $data;
		if($this->autoSave) $this->save();
	}

	/**
	 * @method      remove
	 * @description 移除变量值
	 * @author      HanskiJay
	 * @doenIn      2021-05-04
	 * @param       string      $index 键名
	 */
	public function remove(string $index) : void
	{
		unset($this->config[$index]);
		if($this->autoSave) $this->save();
	}

	/**
	 * @method      save
	 * @description 保存配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string|null      $file 文件
	 * @return      void
	 */
	abstract public function save(?string $file = null) : void;

	/**
	 * @method      reload
	 * @description 重新读取配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      void
	 */
	abstract public function reload() : void;

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
		// Just a method if anywhere need;
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
	 * @method      obj
	 * @description 将当前的数据转换成对象 | Formating currently data($this->config) to Object
	 * @author      HanskiJay
	 * @doenIn      2021-01-31
	 * @return      object
	 */
	public function obj() : object
	{
		return (object) $this->config;
	}
}
