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

use owoframe\helper\Helper;
use owoframe\exception\FileMissedException;
use owoframe\exception\OwOFrameException;

class Cache
{
	/**
	 * 缓存池
	 *
	 * @access protected
	 * @var array
	 */
	protected static $cachePool = [];

	/**
	 * 当前缓存指针
	 *
	 * @access protected
	 * @var string
	 */
	protected static $currentIndex;

	/**
	 * 最大内存占用数(MB)
	 *
	 * @var integer
	 */
	public static $maximumMemory = 10;




	private function __construct()
	{
		// Forbidden instantiate this object;
	}


	/**
	 * 判断缓存存储标签是否存在
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string  $savedTag
	 * @return boolean
	 */
	public static function isSavedTagExists(string $savedTag) : bool
	{
		return isset(static::$cachePool[$savedTag]);
	}

	/**
	 * 设置当前缓存指针
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string  $savedTag
	 * @return boolean
	 */
	public static function setCurrentIndex(string $savedTag) : bool
	{
		if(!static::isSavedTagExists($savedTag)) {
			return false;
		}
		static::$currentIndex = $savedTag;
		return true;
	}

	/**
	 * 返回当前缓存池指针
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string|null
	 */
	public static function getCurrentIndex() : ?string
	{
		return static::$currentIndex ?? null;
	}

	/**
	 * 返回缓存池
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return array
	 */
	public static function getPools() : array
	{
		return static::$cachePool;
	}

	#-------------------------------------------------------------#
	#-----------------------[内存操作方法]-----------------------#
	#-------------------------------------------------------------#
	/**
	 * 返回允许缓存最大的内存大小
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return integer
	 */
	public static function getMaximumMemoryAllowed() : int
	{
		return static::$maximumMemory * 1024 * 1024;
	}

	/**
	 * 获取当前内存占用
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return float
	 */
	public static function getTotalCachedMemory() : float
	{
		return Helper::getCurrentMemoryUsage(false) - CACHE_START_MEMORY;
	}

	#-------------------------------------------------------------#
	#-----------------------[类基本操作方法]-----------------------#
	#-------------------------------------------------------------#
	/**
	 * 从缓存池中读取指定的缓存(不存在则创建)
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string $savedTag
	 * @return object
	 */
	public static function read(string $savedTag) : object
	{
		$_ = static::isSavedTagExists($savedTag) ? $savedTag : static::getCurrentIndex();
		if(!is_string($_)) {
			self::create($savedTag);
		} else {
			$savedTag = $_;
		}
		static::setCurrentIndex($savedTag);
		return static::$cachePool[$savedTag];
	}

	/**
	 * 从缓存池删除一个缓存区
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string  $savedTag
	 * @return boolean
	 */
	public static function close(string $savedTag) : bool
	{
		if(!static::isSavedTagExists($savedTag)) {
			return false;
		}
		unset(static::$cachePool[$savedTag]);
		if(static::getCurrentIndex() === $savedTag) {
			// 将当前缓存池指针指向第一位元素 | Point the current cachePool pointer to the first element;
			reset(static::$cachePool);
			static::setCurrentIndex(key(static::$cachePool));
		}
		return true;
	}

	/**
	 * 保存缓存数据到本地
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string|null $savedTag
	 * @param  string      $path
	 * @return JSON|null
	 */
	/** */
	public static function save(?string $savedTag = null, string $path = F_CACHE_PATH) : ?JSON
	{
		if(static::isSavedTagExists($savedTag ?? static::getCurrentIndex())) {
			$savedTag = static::getCurrentIndex();
			if(!is_dir($path)) {
				throw new OwOFrameException('[CACHE-SAVER] Path \'' . $path . '\' does not exists!');
			}
			$json = new JSON($path . 'cached_zone_' . $savedTag . date('Y_m_d_H_i_s') . '.json', static::$cachePool[$savedTag]->getAll(), true);
			return $json;
		}
		return null;
	}

	/**
	 * 读取文件到缓存
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string $file       文件路径
	 * @param  string $toSavedTag 到缓存区存储名称
	 * @return void
	 */
	public static function load(string $file, string $toSavedTag) : void
	{
		$error = function($message) use ($file) {
			return new FileMissedException('Could not load file \'' . $file . '\': ' . $message);
		};

		if(!file_exists($file)) {
			throw $error('File does not exists!');
		}
		if(strpos('json', $file) === false) {
			throw $error('File must be JSON Format!');
		}

		self::create($toSavedTag, json_decode(file_get_contents($file), true));
	}

	/**
	 * 创建新的缓存区
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @param  string $savedTag
	 * @param  array  $data
	 * @return object
	 */
	final public static function create(string $savedTag, array $data = []) : object
	{
		// 记录启始内存占用 | Record start memory usage;
		if(!defined('CACHE_START_MEMORY')) {
			define('CACHE_START_MEMORY', Helper::getCurrentMemoryUsage(false));
		}
		if(static::getMaximumMemoryAllowed() - static::getTotalCachedMemory() <= 0) {
			throw new OwOFrameException('The memory occupied by the current CachePool has exceeded the warning value, and it is not allowed to continue to create a CacheZone!');
		}
		if(static::isSavedTagExists($savedTag)) {
			throw new OwOFrameException('[CACHE-SAVER] Cache Save Tag(CST) \'' . $savedTag . '\' is already occupied, please change a new CST!');
		}

		return static::$cachePool[$savedTag] = new class($data) {
			/**
			 * 缓存区
			 *
			 * @access private
			 * @var array
			 */
			private $cache;


			public function __construct(array $data = [])
			{
				$this->setAll($data);
			}

			/**
			 * 保存数据到缓存
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @param  array $data
			 * @return void
			 */
			public function setAll(array $data) : void
			{
				$this->cache = $data;
			}

			/**
			 * 添加数据到缓存区
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @param  string  $index
			 * @param  mixed   $data
			 * @param  boolean $overwrite
			 * @return void
			 */
			public function set(string $index, $data, bool $overwrite = false) : void
			{
				if(!$this->exists($index) || $overwrite) {
					$this->cache[$index] = $data;
				}
			}

			/**
			 * 从缓存区删除数据
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @param  string $index
			 * @return void
			 */
			public function del(string $index) : void
			{
				if($this->exists($index)) {
					unset($this->cache[$index]);
				}
			}

			/**
			 * 判断缓存区是否存在一条数据
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @param  string $index
			 * @return boolean
			 */
			public function exists(string $index) : bool
			{
				return isset($this->cache[$index]);
			}

			/**
			 * 返回缓存区所有数据
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @return array
			 */
			public function getAll() : array
			{
				return $this->cache;
			}

			/**
			 * 从缓存区返回一条数据
			 *
			 * @author HanskiJay
			 * @since  2021-11-05
			 * @param  string $index
			 * @param  mixed  $default 默认返回数据
			 * @return mixed
			 */
			public function get(string $index, $default = null)
			{
				return $this->cache[$index] ?? $default;
			}
		};
	}
}