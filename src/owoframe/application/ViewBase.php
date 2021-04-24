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
namespace owoframe\application;

use owoframe\helper\Helper;
use owoframe\route\RouteResource;
use owoframe\exception\InvalidRouterException;

class ViewBase extends ControllerBase
{
	/* @string 视图名称 */
	private $viewName = '';
	/* @string 视图文件扩展 */
	private $fileExt = 'html';
	/* @string 视图模板 */
	protected static $viewTemplate = null;
	/* @array 模板绑定的变量 */
	protected static $bindValues = [];
	/* @array 模板绑定变量到静态资源路径 */
	protected static $bindResources = [];

	/**
	 * @method      assign
	 * @description 将View(V)模板中的变量替换掉
	 * @description Change the value in View(V) template
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string|array      $searched 需要替换的变量名
	 * @param       mixed             $val      替换的值
	 * @return      boolean
	*/
	public function assign($searched, $val = null) : bool
	{
		if(!$this->isValid()) {
			return false;
		}
		if(is_array($searched)) {
			self::$bindValues = array_merge(self::$bindValues, $searched);
		} else {
			self::$bindValues[$searched] = $val;
		}
		return true;
	}
	/**
	 * @method      bindComponent
	 * @description 将View(V)模板中某个指定的变量中的原始变量按照第二参数替换掉
	 * @description Change the value in View(V) template
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $searched 需要替换的变量名
	 * @param       mixed       $val      替换的值
	 * @return      boolean
	*/
	public function bindComponent(string $searched, string $val) : bool
	{
		if(!$this->isValid()) {
			return false;
		}
		if(preg_match("/{\\\$COMPONENT\.{$searched}\.def\[(.*)\]}/", self::$viewTemplate, $match)) {
			$def = (strpos($match[1], ":null") === 0) ? '' : $match[1];
			self::$viewTemplate = str_replace($match[0], $val ?? $def, self::$viewTemplate);
		}
		return true;
	}

	/**
	 * @method      getValue
	 * @description 获取绑定标签的值
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $searched 查找到的变量索引
	 * @return      mixed
	*/
	public function getValue(string $searched)
	{
		return self::$bindValues[$searched] ?? null;
	}

	/**
	 * @method      setStatic
	 * @description 将View(V)模板中的变量替换掉
	 * @description Change the value in View(V) template
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $type     静态资源类型 (css,js,img)
	 * @param       string      $searched 需要替换的变量名
	 * @param       mixed       $val      替换的值
	 * @return      boolean
	*/
	public function setStatic(string $type, string $searched, string $val) : bool
	{
		if(!$this->isValid()) {
			return false;
		}
		self::$bindResources[strtolower($type)][$searched] = $val;
		return true;
	}

	/**
	 * @method      setFileExtension
	 * @description 设置当前视图文件扩展
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $fileExt 视图文件扩展
	 * @return      void
	*/
	public function setFileExtension(string $fileExt) : void
	{
		$fileExt = array_filter(explode(".", $fileExt));
		$this->fileExt = array_shift($fileExt);
	}

	/**
	 * @method      getFileExtension
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @description 获取当前视图文件扩展
	 * @return      string
	*/
	public function getFileExtension() : string
	{
		return $this->fileExt;
	}

	/**
	 * @method      setName
	 * @description 设置当前视图名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $viewName 视图名称
	 * @return      void
	*/
	public function setName(string $viewName, ...$args) : void
	{
		$this->viewName = $viewName;
		if(count($args) > 0) {
			$this->fileExt = array_shift($args);
		}
	}

	/**
	 * @method      getName
	 * @description 返回当前视图名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      string
	*/
	public function getName() : string
	{
		return $this->viewName;
	}

	/**
	 * @method      getCompleteName
	 * @description 返回当前视图完整文件名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      string
	*/
	public function getCompleteName() : string
	{
		return $this->getName() . "." . $this->getFileExtension();
	}

	/**
	 * @method      parse
	 * @description 解析前端模板存在的基本语法
	 * @author      HanskiJay
	 * @doenIn      2021-01-03
	 * @param       string      $text 需要解析的文本
	 * @return      void
	 */
	public static function parse(string &$text) : void
	{
		$regexArray =
		[

		];
	}

	/**
	 * @method      getView
	 * @description 返回当前视图模板(原始数据)
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       bool      $updateCached 更新缓存
	 * @return      null|string
	*/
	public function getView(bool $updateCached = false) : ?string
	{
		if(empty(self::$viewTemplate) || $updateCached) {
			self::$viewTemplate = $this->hasViewPath($this->getCompleteName()) ? file_get_contents($this->getViewPath($this->getCompleteName())) : null;
		}
		return self::$viewTemplate;
	}

	/**
	 * @method      render
	 * @description 渲染视图到前端
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      void
	*/
	public function render() : void
	{
		if(empty(self::$viewTemplate)) return;
		// self::parse(self::$viewTemplate);
		self::bindResources($routeUrls);
		foreach($routeUrls as $type => $urls) {
			$type = strtoupper($type);
			foreach($urls as $name => $url) {
				self::$viewTemplate = str_replace("{\${$type}.{$name}}", $url, self::$viewTemplate);
			}
		}
		foreach(self::$bindValues as $k => $v) {
			self::$viewTemplate = str_replace("{\${$k}}", $v, self::$viewTemplate);
		}
		if(preg_match_all("/{\\\$(.*)\.def\[(.*)\]}/", self::$viewTemplate, $matches))
		{
			foreach($matches[1] as $k => $v) {
				$match = $matches[2][$k];
				$def   = (strpos($match, ':null') === 0) ? '' : $match;
				self::$viewTemplate = str_replace("{\${$v}.def[{$match}]}", self::$bindValues[$v] ?? $def, self::$viewTemplate);
			}
		}
		// 替换html-link标签(当owoLink中的actived属性为false时, 删除该标签);
		if(preg_match_all("/<owoLink (.*)>/im", self::$viewTemplate, $matches))
		{
			foreach($matches[1] as $key => $sub) {
				if(preg_match("/actived=\"([^ ]*)\"/im", $sub, $match)) {
					if(strtolower($match[1]) === "false") {
						$matches[1][$key] = '';
					} else {
						$matches[1][$key] = "<link " . trim(str_replace($match[0], '', $matches[1][$key])) . '>';
					}
					self::$viewTemplate = str_replace($matches[0][$key], $matches[1][$key], self::$viewTemplate);
				}
			}
		}
		// 替换html-script标签(当owoScript中的actived属性为false时, 删除该标签);
		if(preg_match_all("/<owoScript (.*)>/im", self::$viewTemplate, $matches))
		{
			foreach($matches[1] as $key => $sub) {
				if(preg_match("/actived=\"([^ ]*)\"/im", $sub, $match)) {
					if(strtolower($match[1]) === "false") {
						$matches[1][$key] = '';
					} else {
						$matches[1][$key] = "<script " . trim(str_replace($match[0], '', $matches[1][$key])) . '>';
					}
					self::$viewTemplate = str_replace($matches[0][$key], $matches[1][$key], self::$viewTemplate);
				}
			}
		}
	}

	private static function bindResources(&$handled) : void
	{
		$handled = [];
		foreach(self::$bindResources as $group => $resources) {
			foreach($resources as $tag => $resource) {
				if(!is_file($resource)) {
					throw new InvalidRouterException("Resource path '{$resource}' doesn't exists!");
				}
				/*$finfo    = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $resource);
				finfo_close($finfo);*/
				$type = @end(explode('.', $resource));
				if(!isset(Helper::MIMETYPE[$type])) {
					throw new UnknownErrorException('No file mimetype');
				}
				// $handled[$group][$tag] = $newTag;
				$basePath = F_CACHE_PATH . $group . DIRECTORY_SEPARATOR;
				if(!is_dir($basePath)) mkdir($basePath, 755, true);
				$hashTag  = md5($resource);
				$basePath = "{$basePath}{$hashTag}.php";
				if(!file_exists($basePath)) {
					// TODO: Cache static files;
					file_put_contents($basePath, "<?php /* Cached in " . date("Y-m-d H:i:s") . "@{$hashTag} */ header('Content-Type: " . Helper::MIMETYPE[$type] . "'); echo file_get_contents('{$resource}'); ?>");
				}
				$handled[$group][$tag] = "/static.php/{$group}/{$hashTag}.{$type}";
			}
		}
	}

	/**
	 * @method      isValid
	 * @description 判断当前是否存在一个有效的视图模板
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	 * @return      boolean
	*/
	public function isValid() : bool
	{
		return !empty(self::$viewTemplate);
	}

	/**
	 * @method      getComponentPath
	 * @description 获取组件资源目录
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	*/
	public function getComponentPath(string $index) : string
	{
		return $this->getViewPath('component') . DIRECTORY_SEPARATOR . $index . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getComponent
	 * @description 获取组件资源目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $folder 文件目录
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getComponent(string $folder, string $index) : string
	{
		$file = $this->getComponentPath($folder) . $index;
		if(!file_exists($file)) {
			return '';
		}
		return file_get_contents($file);
	}

	/**
	 * @method      getCssPath
	 * @description 获取CSS文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getCssPath(string $index) : string
	{
		return $this->getStaticPath('css') . $index;
	}

	/**
	 * @method      getPublicCssPath
	 * @description 获取公共目录下的CSS文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getPublicCssPath(string $index) : string
	{
		return $this->getResourcePath('css') . $index;
	}

	/**
	 * @method      getJsPath
	 * @description 获取JS文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getJsPath(string $index) : string
	{
		return $this->getStaticPath('js') . $index;
	}

	/**
	 * @method      getPublicJsPath
	 * @description 获取公共目录下的JS文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getPublicJsPath(string $index) : string
	{
		return $this->getResourcePath('js') . $index;
	}

	/**
	 * @method      getImgPath
	 * @description 获取IMG文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getImgPath(string $index) : string
	{
		return $this->getStaticPath('img') . $index;
	}

	/**
	 * @method      getPublicImgPath
	 * @description 获取公共目录下的IMG文件目录的指定文件
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	 * @return      string
	*/
	public function getPublicImgPath(string $index) : string
	{
		return $this->getResourcePath('img') . $index;
	}

	/**
	 * @method      existsStatic
	 * @description 判断是否存在一个局部静态资源文件目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index1 文件夹索引
	 * @param       string      $index2 文件索引
	 * @return      boolean
	*/
	public function existsStatic(string $index1, string $index2) : bool
	{
		$index1 = strtolower($index1);
		switch($index1)
		{
			default:
			return false;

			case 'css':
			return is_file($this->getCssPath($index2));

			case 'js':
			case 'javascript':
			return is_file($this->getJsPath($index2));

			case 'img':
			case 'image':
			return is_file($this->getImgPath($index2));

			case 'compo':
			case 'component':
			return is_dir($this->getComponentPath($index2));
		}
	}
}