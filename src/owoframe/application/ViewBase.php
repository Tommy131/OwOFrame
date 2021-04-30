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
use owoframe\exception\ParameterErrorException;

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
	 * @method      removeValue
	 * @description 删除模板中的变量定义
	 * @author      HanskiJay
	 * @doenIn      2021-04-26
	 * @param       string      $searched 需要替换的变量名
	 * @return      void
	 */
	public function removeValue(string $searched) : void
	{
		if(!$this->isValid()) {
			return;
		}
		if(isset(self::$bindValues[$searched])) {
			unset(self::$bindValues[$searched]);
		}
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
	 * @method      setViewName
	 * @description 设置当前视图名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $viewName 视图名称
	 * @return      void
	*/
	public function setViewName(string $viewName, ...$args) : void
	{
		$this->viewName = $viewName;
		if(count($args) > 0) {
			$this->fileExt = array_shift($args);
		}
	}

	/**
	 * @method      getViewName
	 * @description 返回当前视图名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      string
	*/
	public function getViewName() : string
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
		return $this->getViewName() . "." . $this->getFileExtension();
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
	 * @method      parseLoopArea
	 * @description 解析前端模板存在的循环语法
	 * @author      HanskiJay
	 * @doenIn      2021-01-03
	 * @param       string      $loopArea 需要解析的文本
	 * @return      void
	 */
	public function parseLoopArea(string &$loopArea) : void
	{
		// $bindElement = "\\\$";
		$bindElement = '@';
		$loopHead    = "\{loop {$bindElement}([a-zA-Z0-9]+) in \\\$([a-zA-Z0-9]+)\}";
		$loopBetween = "([\s\S]*)";
		$loopEnd     = "\{\/loop\}";
		$loopRegex   = "/{$loopHead}{$loopBetween}{$loopEnd}/mU";

		if(!preg_match_all($loopRegex, $loopArea, $matched, PREG_SET_ORDER, 0)) {
			return;
		}
		foreach($matched as $loopGroup) {
			$bindTag  = trim($loopGroup[2]);                // 绑定的数组变量到模板;
			$defined  = trim($loopGroup[1]);                // 定义的变量到模板;
			// $loopArea = trim(preg_replace("/{$loopEnd}/im", '', preg_replace("/{$loopHead}/im", '', $loopArea)));
			// $loopArea = explode("\n", trim($loopArea));      // 匹配到的循环语句;
			$loop     = explode("\n", trim($loopGroup[0]));      // 匹配到的循环语句;

			$data = $this->getValue($bindTag);
			if(!$data || ($data && !is_array($data))) {
				// TODO: 增加一个是否为 DEBUG_MODE 模式, 根据情况扔出异常;
				throw new ParameterErrorException("Cannot find bindTag {{$bindElement}{$bindTag}} !");
			}

			$data = array_filter($data);
			$complied = [];
			$finaly   = '';
			foreach($data as $k => $v) {
				if(!is_array($v)) {
					throw new ParameterErrorException('不合法的使用方法!');
				}

				foreach($loop as $n => $line) {
					if(preg_match("/({$loopEnd}|{$loopHead})/im", $line)) {
						continue;
					}
					$line   = trim($line);
					$length = strlen($line);
					if(preg_match_all("/{$defined}?([\\\.]?[a-zA-Z0-9]){0,$length}/", $line, $match)) {
						$matchedTags = array_shift($match);    // 获取到的原始绑定标签集;
						foreach($matchedTags as $matchedTag) {
							$parseArray = explode('.', $matchedTag); // 解析并分级绑定标签;
							array_shift($parseArray);                // 去除第一级原始绑定标签;

							if((count($parseArray) === 0) && (($num = count($data)) > 1)) {
								$complied[$k][$n] = str_replace($bindElement . $matchedTag . '@', "Array(n:{$bindElement}{$bindTag})[{$num}]", $line);
							} else {
								$current = $v;
								while($parseArray) {
									$next = array_shift($parseArray);
									if(is_array($current)) {
										if(isset($current[$next])) {
											$current = $current[$next];
										} else {
											$current = "Array(undefined:{$bindElement}{$bindTag}.{$next})";
										}
									}
								}
								$complied[$k][$n] = str_replace($bindElement . $matchedTag . '@', $current, $complied[$k][$n] ?? $line);
							}
						}
					} else {
						$complied[$k][$n] = $line;
					}
				}
				ksort($complied[$k]);
				foreach($complied[$k] as $result) {
					$finaly .= $result . PHP_EOL;
				}
			}
		}
		$this->removeValue($bindTag);
		$loopArea = preg_replace($loopRegex, $finaly, $loopArea);
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
		$this->parseLoopArea(self::$viewTemplate);
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
		// 替换成html-link和html-script标签(当owoLink|owoScript中的actived属性为false时, 删除该标签);
		if(preg_match_all("/<(owoLink|owoScript) (.*)>/m", self::$viewTemplate, $matches))
		{
			foreach($matches[0] as $key => $found) {
				$found   = trim($found);
				$newLine = '';
				if(preg_match("/actived=\"([^ ]*)\"[ ]?/im", $found, $match)) {
					if(strtolower($match[1]) === "true") {
						$matchedTag = $matches[1][$key];
						switch($matchedTag) {
							default:
								$tag = 'div';
							break;

							case 'owoScript':
								$tag = 'script';
							break;
							
							case 'owoLink':
								$tag = 'link';
							break;
						}
						$newLine = str_replace([$matchedTag, $match[0]], [$tag, ''], $found);
					}
					self::$viewTemplate = str_replace($found, $newLine, self::$viewTemplate);
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
					$charset = ((stripos(Helper::MIMETYPE[$type], 'application') !== false) || (stripos(Helper::MIMETYPE[$type], 'text') !== false))
								? '; charset=utf-8' : null;
					file_put_contents($basePath, "<?php /* Cached in " . date("Y-m-d H:i:s") . "@{$hashTag} */ header('Content-Type: " . Helper::MIMETYPE[$type] . "" . $charset . "'); header('X-Content-Type-Options: nosniff'); header('Cache-Control: max-age=31536000, immutable'); echo file_get_contents('{$resource}'); ?>");
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