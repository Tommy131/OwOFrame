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
use owoframe\http\route\Router;
use owoframe\exception\InvalidRouterException;
use owoframe\exception\ParameterErrorException;

class ViewBase extends ControllerBase
{
	/* @string 视图文件路径 */
	protected $filePath = '';
	/* @string 视图模板 */
	protected $viewTemplate = '';
	/* @array 模板绑定的变量 */
	protected $bindValues = [];
	/* @array 绑定常量到模板 */
	protected $customConstants = [];


	public function init(string $filePath = '', bool $update = false) : void
	{
		if(!empty($this->viewTemplate) && !$update) {
			return;
		}
		if(!file_exists($filePath)) {
			$controllerName = Router::getParameters(-1);
			switch(count($controllerName)) {
				case 0:
					$controllerName = DEFAULT_APP_NAME;
				break;

				case 1:
					$controllerName = array_shift($controllerName);
				break;

				case 2:
					$controllerName = end($controllerName);
				break;

				default:
				case 3:
					$controllerName = array_slice($controllerName, 1, 1);
					$controllerName = array_shift($controllerName);
				break;
			}
			$controllerName = ucfirst(strtolower($controllerName));
			if(!Router::getCurrentApp()->getController($controllerName)) {
				$controllerName = Router::getCurrentApp()->getDefaultController(true);
			}
			$this->filePath = $this->getViewPath($controllerName . '.html');
		} else {
			$this->filePath = $filePath;
		}

		$this->viewTemplate = file_get_contents($this->filePath);
	}

	/**
	 * @method      assign
	 * @description 将View(V)模板中的变量替换掉
	 * @description Change the value in View(V) template
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string|array      $searched 需要替换的变量名
	 * @param       mixed             $val      替换的值
	 * @return      void
	*/
	public function assign($searched, $val = null) : void
	{
		if(is_array($searched)) {
			$this->bindValues = array_merge($this->bindValues, $searched);
		} else {
			$this->bindValues[$searched] = $val;
		}
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
		if(isset($this->bindValues[$searched])) {
			unset($this->bindValues[$searched]);
		}
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
		return $this->bindValues[$searched] ?? null;
	}



	/**
	 * @method 模板渲染核心方法
	 */
	/**
	 * @method      parseResourcePath
	 * @description 解析模板中的资源路径绑定
	 * @author      HanskiJay
	 * @doenIn      2021-05-25
	 * @param       string            &$str 传入模板
	 * @return      void
	 */
	protected function parseResourcePath(string &$str) : void
	{
		$regex = 
		[
			'/<(img|script|link) (.*)>/imU',
			'/@(src|href)="{\$(\w*)\|(.*)}"/imU',
			'/@name="(\w*)"[\s]+?/mU',
			'/@actived="(\w*)"[\s]+?/imU'
		];

		if(!preg_match_all($regex[0], $str, $matches)) {
			return;
		}
		
		foreach($matches[0] as $key => $tag) {
			if(preg_match($regex[2], $tag, $match)) {
				$name = $match[1];
			} else {
				$name = '';
			}

			if(($actived = $this->getValue($name)) !== null) {
				$actived = (($actived === true) || ($actived === 'true')) ? true : false;
			}
			elseif(preg_match($regex[3], $tag, $match)) {
				$actived = ($match[1] === 'true') ? true : (($match[1] === 'false') ? false : null);
			} else {
				$actived = null;
			}

			if(is_bool($actived) && !$actived) {
				if($matches[1][$key] === 'script') {
					$tag = "{$tag}</script>";
				}
				$tag = str_replace(['.', '/', '|', '$'], ["\.", '\/', '\|', '\$'], $tag);
				$str = preg_replace("/(\s*?){1}{$tag}/i", '', $str);
			} else {
				$newTag = preg_replace($regex[3], '', preg_replace($regex[2], '', $tag));
				$str    = str_replace($tag, $newTag, $str);

				if(preg_match($regex[1], $tag, $match)) {
					$type = strtoupper($match[2] ?? 'unknown');
					$file = $match[3];
					$path = '';
					switch($type)
					{
						case 'CSS':
						case 'CSSPATH':
							$path .= $this->getStaticPath('css');
						break;
						case 'RCSS':
						case 'RCSSPATH':
							$path .= $this->getResourcePath('css');
						break;

						case 'JS':
						case 'JSPATH':
							$path .= $this->getStaticPath('js');
						break;
						case 'RJS':
						case 'RJSPATH':
							$path .= $this->getResourcePath('js');
						break;

						case 'IMG':
						case 'IMGPATH':
							$path .= $this->getStaticPath('img');
						break;
						case 'RIMG':
						case 'RIMGPATH':
							$path .= $this->getResourcePath('img');
						break;

						case 'PACKAGE':
						case 'PKGPATH':
							$path .= $this->getStaticPath('package');
						break;
					}

					$src = $this->generateStaticUrl($path . $file);
					$str = str_replace($match[0], $match[1] . "=\"{$src}\"", $str);
				}
			}
		}
	}

	/**
	 * @method      parseLoopArea
	 * @description 解析前端模板存在的循环语法
	 * @author      HanskiJay
	 * @doenIn      2021-01-03
	 * @param       string      $loopArea 需要解析的文本
	 * @return      void
	 */
	protected function parseLoopArea(string &$loopArea) : void
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
				if(DEBUG_MODE) {
					throw new ParameterErrorException("Cannot find bindTag {{$bindElement}{$bindTag}} !");
				}
				return;
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
	 * @method      readString
	 * @description 解析字符串的传参
	 * @author      HanskiJay
	 * @doenIn      2021-05-29
	 * @param       string      $str     待解析的字符串
	 * @param       string      &$result
	 * @return      boolean
	 */
	protected function readString(string $str, &$result = '') : bool
	{
		if(count($arr = explode('=', $str)) > 1) {
			$type = strtolower(array_shift($arr));
			switch($type) {
				case 'get_file':
					$path = array_shift($arr);
					if(is_file($path)) {
						 $result = file_get_contents($path);
						 return true;
					}
				break;
			}
		}
		return false;
	}

	/**
	 * @method      replaceBindValue
	 * @description 替换绑定变量
	 * @author      HanskiJay
	 * @doenIn      2021-05-29
	 * @param       string                     $key   变量名
	 * @param       int|integer|string|boolean $value 变量值
	 * @param       string                     &$str  原始字符串
	 * @return      void
	 */
	protected function replaceBindValue(string $key, $value, string &$str) : void
	{
		if(!is_int($value) && !is_string($value) && !is_bool($value)) {
			return;
		}
		if(preg_match('/{\$' . $key . '\|def\[(.*)\]}/mU', $str, $match)) {
			if($this->readString($str, $result)) {
				$str = str_replace($match[0], $result, $str);
			} else {
				$str = str_replace($match[0], $value ?? (($match[1] === ':null') ? '' : $match[1]), $str);
			}
		}
		$str = str_replace("{\${$key}}", $value, $str);
	}

	/**
	 * @method      render
	 * @description 渲染视图到前端
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      string
	*/
	protected function render() : string
	{
		// 获取模板;
		$this->init();

		/* 开始解析模板组件 */
		$regex = '/{require (.*)}/imU';
		while(preg_match_all($regex, $this->viewTemplate, $matches)) {
			foreach($matches[1] as $key => $path) {
				$path = $this->getViewPath($path);
				Helper::escapeSlash($path);
				if(is_file($path)) {
					$this->viewTemplate = str_replace($matches[0][$key], file_get_contents($path), $this->viewTemplate);
				}
			}
		}
		// 转换常量绑定;
		if(preg_match_all('/{([0-9A-Z_]*)}/mU', $this->viewTemplate, $matches)) {
			foreach($matches[1] as $k => $constName) {
				if(defined($constName) || isset($this->customConstants[$constName])) {
					$this->viewTemplate = str_replace($matches[0][$k], @constant($constName) ?? $this->customConstants[$constName], $this->viewTemplate);
				}
			}
		}
		// 解析循环语句;
		$this->parseLoopArea($this->viewTemplate);
		// 绑定变量;
		foreach($this->bindValues as $k => $v) {
			$this->replaceBindValue($k, $v, $this->viewTemplate);
		}
		// 解析剩余的变量(包含默认值);
		if(preg_match_all('/{\$(.*)\|def\[(.*)\]}/mU', $this->viewTemplate, $matches))
		{
			foreach($matches[1] as $k => $v) {
				$match = $matches[2][$k];

				if($this->readString($match, $result)) {
					$this->viewTemplate = str_replace("{\${$v}|def[{$match}]}", $result, $this->viewTemplate);
				} else {
					$this->viewTemplate = str_replace("{\${$v}|def[{$match}]}", $this->getValue($v) ?? (($match === ':null') ? '' : $match), $this->viewTemplate);
				}
			}
		}
		// 绑定资源路径到路由;
		$this->parseResourcePath($this->viewTemplate);

		return $this->viewTemplate;
	}

	/**
	 * @method      generateStaticUrl
	 * @description 生成静态资源路由地址
	 * @author      HanskiJay
	 * @doenIn      2021-05-29
	 * @param       string            $filePath 静态资源文件路径
	 * @return      string
	 */
	protected function generateStaticUrl(string $filePath) : string
	{
		Helper::escapeSlash($filePath);
		$type     = explode('.', $filePath);
		$type     = strtolower(end($type));

		if(is_file($filePath) && isset(Helper::MIMETYPE[$type]))
		{
			$basePath = F_CACHE_PATH . $type . DIRECTORY_SEPARATOR;
			if(!is_dir($basePath)) mkdir($basePath, 755, true);
			$hashTag  = md5($filePath);
			$basePath = "{$basePath}{$hashTag}.php";

			if(!file_exists($basePath)) {
				// TODO: Cache static files;
				$charset = ((stripos(Helper::MIMETYPE[$type], 'application') !== false) || (stripos(Helper::MIMETYPE[$type], 'text') !== false))
							? '; charset=utf-8' : null;
				file_put_contents($basePath, "<?php /* Cached in " . date("Y-m-d H:i:s") . "@{$hashTag} */ header('Content-Type: " . Helper::MIMETYPE[$type] . "" . $charset . "'); header('X-Content-Type-Options: nosniff'); header('Cache-Control: max-age=31536000, immutable'); echo file_get_contents('" . $filePath . "'); ?>");
			}
			$filePath = "/static.php/{$type}/{$hashTag}.{$type}";
		} else {
			$filePath = null;
		}
		return $filePath ?? '(unknown)';
	}



	/**
	 * @method 静态资源相对/绝对路径获取方法
	 */
	/**
	 * @method      getComponent
	 * @description 获取组件资源
	 * @return      string
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @param       string      $index 文件/文件夹索引
	*/
	public function getComponent(string $index, int $mode = 0) : string
	{
		$path = $this->getViewPath('component') . DIRECTORY_SEPARATOR . Helper::escapeSlash($index);
		return ($mode === 0) ? $path : (is_file($path) ? file_get_contents($path) : "[VIEW-COMPONENT] File '{$path}' Not Found");
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
		return $this->getStaticPath('css') . Helper::escapeSlash($index);
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
		return $this->getResourcePath('css') . Helper::escapeSlash($index);
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
		return $this->getStaticPath('js') . Helper::escapeSlash($index);
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
		return $this->getResourcePath('js') . Helper::escapeSlash($index);
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
		return $this->getStaticPath('img') . Helper::escapeSlash($index);
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
		return $this->getResourcePath('img') . Helper::escapeSlash($index);
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
			return is_dir($this->getComponent($index2));
		}
	}

	/**
	 * @method      getTemplate
	 * @description 返回模板
	 * @author      HanskiJay
	 * @doenIn      2021-05-29
	 * @return      null|string
	 */
	protected function &getTemplate() : ?string
	{
		return $this->viewTemplate;
	}
}