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
namespace owoframe\http\route;

use owoframe\helper\Helper;
use owoframe\exception\{RouterException, UnknownErrorException};

class RouteResource
{
	/* @RouteRource 本类实例接口 */
	private static $instance = null;
	/* @array 绑定的资源路径到Url */
	private $bindData = [];
	/* @array 生成的Url集合 */
	public static $urls = [];

	public static function bindResources(array $static, &$handled) : void
	{
		$handled = [];
		foreach($static as $group => $resources) {
			foreach($resources as $tag => $resource) {
				if(!is_file($resource)) {
					throw new RouterException("Resource path '{$resource}' doesn't exists!");
				}
				/*$finfo    = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $resource);
				finfo_close($finfo);*/
				$mimetype = @end(explode('.', $resource));
				if(!isset(Helper::MIMETYPE[$mimetype])) {
					throw new UnknownErrorException('No file mimetype');
				}
				$mimetype = Helper::MIMETYPE[$mimetype];
				self::bindGroup($group, $resource, $newTag);
				// $handled[$group][$tag] = $newTag;
				$basePath = CACHE_PATH . $group . DIRECTORY_SEPARATOR;
				if(!is_dir($basePath)) mkdir($basePath, 755, true);
				$newTag   = md5($newTag);
				$basePath = $basePath . "{$newTag}.php";
				if(!file_exists($basePath)) {
					file_put_contents($basePath, '<?php /* Cached in '.date("Y-m-d H:i:s").'@'.$newTag.' */ header("Content-Type: '.$mimetype.';"); echo file_get_contents(\''.$resource.'\'); ?>');
				}
				// TODO: 在public目录下添加static.php引导至cache里的静态资源绑定;
				$handled[$group][$tag] = "/static.php/{$group}/{$newTag}";
			}
		}
	}

	/**
	 * @method      bind
	 * @description 绑定某个标签(公有属性);
	 * @access      public
	 * @param       string[tag|绑定标签]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function bind(string $resource, &$handled) : void
	{
		$handled = md5($resource);
		if(!self::getInstance()->hasBind($handled)) {
			self::getInstance()->set($handled, $resource);
		} else {
			$handled = 'none';
		}
	}

	/**
	 * @method      unbind
	 * @description 解除绑定某个标签(公有属性);
	 * @access      public
	 * @param       string[tag|绑定标签]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function unbind(string $tag) : void
	{
		if(self::getInstance()->hasBind($tag)) {
			self::getInstance()->unset($tag);
		}
	}

	/**
	 * @method      bindGroup
	 * @description 绑定某个标签到组(公有属性);
	 * @access      public
	 * @param       string[groupName|组名]
	 * @param       string[tag|绑定标签]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function bindGroup(string $groupName, string $resource, &$handled) : bool
	{
		$handled = md5($resource);
		self::getInstance()->addGroup($groupName);
		return self::getInstance()->joinIn($groupName, $handled, $resource);
	}

	/**
	 * @method      unbindGroup
	 * @description 从组解除绑定某个标签(公有属性);
	 * @access      public
	 * @param       string[groupName|组名]
	 * @param       string[tag|绑定标签]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function unbindGroup(string $groupName, string $resource) : void
	{
		$handled = md5($resource);
		if(self::getInstance()->hasMember($groupName, $handled)) {
			self::getInstance()->kick($groupName, $handled);
		}
	}

	/**********************************************************/

	/**
	 * @method      getInstance
	 * @description 返回本类实例;
	 * @return      RouteResource
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public static function getInstance() : RouteResource
	{
		if(!static::$instance instanceof RouteResource) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * @method      hasBind
	 * @description 判断某个标签是否被绑定;
	 * @param       string[tag|绑定标签]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function hasBind(string $tag) : bool
	{
		return isset($this->bindData[$tag]);
	}

	/**
	 * @method      set
	 * @description 绑定某个标签(私有属性);
	 * @access      protected
	 * @param       string[tag|绑定标签]
	 * @param       mixed[data|绑定元数据]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	protected function set(string $tag, $data) : void
	{
		$this->bindData[$tag] = $data;
	}

	/**
	 * @method      unset
	 * @description 解除绑定某个标签(私有属性);
	 * @access      protected
	 * @param       string[tag|绑定标签]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	protected function unset(string $tag) : void
	{
		if(isset($this->bindData[$tag])) {
			unset($this->bindData[$tag]);
		}
	}

	/**
	 * @method      hasGroup
	 * @description 判断某个标签是否被绑定;
	 * @param       string[groupName|组名]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function hasGroup(string $groupName) : bool
	{
		return isset($this->bindData[$groupName]);
	}

	/**
	 * @method      addGroup
	 * @description 判断某个标签是否被绑定;
	 * @param       string[groupName|组名]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function addGroup(string $groupName) : bool
	{
		if(!$this->hasGroup($groupName)) {
			$this->bindData[$groupName] = [];
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @method      hasGroup
	 * @description 判断某个标签是否被绑定;
	 * @param       string[groupName|组名]
	 * @param       string[tag|绑定标签]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function hasMember(string $groupName, string $tag) : bool
	{
		return isset($this->bindData[$groupName][$tag]);
	}

	/**
	 * @method      joinIn
	 * @description 绑定某个标签到组(私有属性);
	 * @access      protected
	 * @param       string[groupName|组名]
	 * @param       string[tag|绑定标签]
	 * @param       mixed[data|绑定元数据]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	protected function joinIn(string $groupName, string $tag, $data) : bool
	{
		if($this->hasGroup($groupName)) {
			$this->bindData[$groupName][$tag] = $data;
			return true;
		}
		return false;
	}

	/**
	 * @method      kick
	 * @description 解除绑定某个标签(私有属性);
	 * @access      protected
	 * @param       string[groupName|组名]
	 * @param       string[tag|绑定标签]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	protected function kick(string $groupName, string $tag) : void
	{
		if($this->hasMember($groupName, $tag)) {
			unset($this->bindData[$groupName][$tag]);
		}
	}

	/**
	 * @method      get
	 * @description 获取一个指定的数据;
	 * @param       string[tag|绑定标签]
	 * @return      mixed
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function get(string $tag)
	{
		return $this->bindData[$tag] ?? null;
	}
	/**
	 * @method      getAll
	 * @description 返回所有数据;
	 * @return      array
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function getAll() : array
	{
		return $this->bindData;
	}
}