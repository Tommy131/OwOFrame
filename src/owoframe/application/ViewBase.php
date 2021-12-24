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
namespace owoframe\application;

use owoframe\exception\OwOFrameException;
use owoframe\exception\ParameterTypeErrorException;
use owoframe\helper\Helper;
use owoframe\http\route\Router;

class ViewBase extends ControllerBase
{

	/**
	 * 显示控制区域变量绑定前缀
	 */
	public const DISPLAY_CONTROL_PREFIX = 'display_id_';

	/**
	 * 视图文件路径
	 *
	 * @access protected
	 * @var string
	 */
	protected $filePath = '';

	/**
	 * 视图模板
	 *
	 * @access protected
	 * @var string
	 */
	protected $viewTemplate = '';

	/**
	 * 模板绑定的变量
	 *
	 * @access protected
	 * @var array
	 */
	protected $bindValues = [];

	/**
	 * 绑定常量到模板
	 *
	 * @access protected
	 * @var array
	 */
	protected $constants = [];



	public function __construct(\owoframe\application\AppBase $app)
	{
		parent::__construct($app);
		// Define constant in VIEW Template;
		$this->mergeConstants([
			'VIEW_PATH' => $this->getViewPath('')
		]);
	}

	/**
	 * 合并常量定义
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @return void
	 */
	public function mergeConstants(array $arr) : void
	{
		$this->constants = array_merge($this->constants, $arr);
	}

	/**
	 * 初始化View模板
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $filePath 模板路径
	 * @param  boolean     $update   更新模板并缓存
	 * @return void
	 */
	public function init(string $filePath = '', bool $update = false) : void
	{
		if(!empty($this->viewTemplate) && !$update) {
			return;
		}
		if(!file_exists($filePath)) {
			$controller = Router::getCurrent('controller');
			$controllerName = ucfirst(strtolower($controller->getName()));
			if(!Router::getCurrent('app')->getController($controllerName, false)) {
				$controllerName = Router::getCurrent('app')->getDefaultController(true);
			}
			$this->filePath = $this->getViewPath($controllerName . '.html');
		} else {
			$this->filePath = $filePath;
		}

		$this->viewTemplate = is_file($this->filePath) ? file_get_contents($this->filePath) : '';
	}

	/**
	 * 将View(V)模板中的变量替换掉
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string|array      $searched 需要替换的变量名
	 * @param  mixed             $val      替换的值
	 * @return void
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
	 * 删除模板中的变量定义
	 *
	 * @author HanskiJay
	 * @since  2021-04-26
	 * @param  string      $searched 需要替换的变量名
	 * @return void
	 */
	public function removeValue(string $searched) : void
	{
		if(isset($this->bindValues[$searched])) {
			unset($this->bindValues[$searched]);
		}
	}

	/**
	 * 绑定一个Display控制ID
	 *
	 * @author HanskiJay
	 * @since  2021-12-21
	 * @param  string      $cid     Display区域显示的控制ID
	 * @param  boolean     $status 显示状态
	 * @return void
	 */
	public function assignDisplay(string $cid, bool $status) : void
	{
		$this->bindValues[self::DISPLAY_CONTROL_PREFIX . $cid] = $status;
	}

	/**
	 * 获取一个Display控制ID的状态
	 *
	 * @author HanskiJay
	 * @since  2021-12-21
	 * @param  string      $cid     Display区域显示的控制ID
	 * @return null|boolean
	 */
	public function getDisplay(string $cid) : ?bool
	{
		$cid = self::DISPLAY_CONTROL_PREFIX . $cid;
		return isset($this->bindValues[$cid]) ? (bool) $this->bindValues[$cid] : null;
	}

	/**
	 * 移除一个Display控制ID
	 *
	 * @author HanskiJay
	 * @since  2021-12-21
	 * @param  string      $cid     Display区域显示的控制ID
	 * @return void
	 */
	public function deleteDisplay(string $cid) : void
	{
		$cid = self::DISPLAY_CONTROL_PREFIX . $cid;
		if(isset($this->bindValues[$cid])) {
			unset($this->bindValues[$cid]);
		}
	}

	/**
	 * 获取绑定标签的值
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $searched 查找到的变量索引
	 * @return mixed
	 */
	public function getValue(string $searched)
	{
		return $this->bindValues[$searched] ?? null;
	}



	/**
	 * 模板渲染核心方法
	 */
	/**
	 * 解析模板中的资源路径绑定
	 *
	 * @author HanskiJay
	 * @since  2021-05-25
	 * @param  string      &$str 传入模板
	 * @return void
	 */
	protected function parseResourcePath(string &$str) : void
	{
		$regex =
		[
			'/<(img|script|link) (.*)>/imU',
			'/@(src|href)="{\$(\w*)\|(.*)}"/imU',
			'/@name="(\w*)"[\s]+?/mU',
			'/@active="(\w*)"[\s]+?/imU',
			'/{\$(\w*)\|(.*)}/imU'
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

			if(($active = $this->getValue($name)) !== null) {
				$active = (($active === true) || ($active === 'true')) ? true : false;
			}
			elseif(preg_match($regex[3], $tag, $match)) {
				$active = ($match[1] === 'true') ? true : (($match[1] === 'false') ? false : null);
			} else {
				$active = null;
			}

			if(is_bool($active) && !$active) {
				if($matches[1][$key] === 'script') {
					$tag = "{$tag}</script>";
				}
				// TODO: 支持其他标签使用 @active 元素;
				$tag = str_replace(['.', '/', '|', '$'], ['\.', '\/', '\|', '\$'], $tag);
				$str = preg_replace("/(\s*?){1}{$tag}/i", '', $str);
			} else {
				$newTag = preg_replace($regex[3], '', preg_replace($regex[2], '', $tag));
				$str    = str_replace($tag, $newTag, $str);

				if(preg_match($regex[1], $tag, $match)) {
					$type = strtoupper($match[2] ?? 'unknown');
					$file = $match[3];
					$path = '';

					$this->take($type, $path);

					$src = $this->generateStaticUrl($path . $file);
					$str = str_replace($match[0], $match[1] . "=\"{$src}\"", $str);
				}
			}
		}
		if(preg_match_all($regex[4], $str, $matches)) {
			foreach($matches[0] as $key => $tag) {
				$path = '';
				$this->take($matches[1][$key], $path);
				$src = $this->generateStaticUrl($path . $matches[2][$key]);
				$str = str_replace($matches[0][$key], $src, $str);
			}
		}
	}

	/**
	 * 获取资源定义类型及返回路径
	 *
	 * @author HanskiJay
	 * @since  2021-01-03
	 * @param  string $type
	 * @param  &      $path
	 * @return void
	 */
	protected function take(string $type, &$path) : void
	{
		switch(strtoupper($type))
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
	}

	/**
	 * 解析前端模板存在的循环语法
	 *
	 * @author HanskiJay
	 * @since  2021-01-03
	 * @param  string      $loopArea 需要解析的文本
	 * @return void
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
				throw new OwOFrameException("[View-LoopParserError] Cannot find bindTag {\${$bindTag}} !");
			}

			$data = array_filter($data);
			$complied = [];
			$finally   = '';
			foreach($data as $k => $v) {
				if(!is_array($v)) {
					throw new ParameterTypeErrorException('不合法的使用方法!', 'array', $v);
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
					$finally .= $result . PHP_EOL;
				}
			}
		}
		$this->removeValue($bindTag);
		$loopArea = preg_replace($loopRegex, $finally, $loopArea);
	}

	/**
	 * 解析前端模板存在的区域控制显示语法
	 *
	 * @author HanskiJay
	 * @since  2021-12-21
	 * @param  string      $str 需要解析的文本
	 * @return void
	 */
	protected function parseDisplayArea(string &$str) : void
	{
		if(preg_match_all('/<\!--@display=(true|false)?(\|@cid=(\w+))?-->(.*)<\!--@display-->/imsU', $str, $matches)) {
			foreach($matches[0] as $k => $v) {
				// 区域ID绑定解析;
				$cid = $matches[3][$k];
				// 区域display默认状态 (布尔值);
				$display = strtolower($matches[1][$k]);
				$display = ($display === 'true') ? true : false;
				if(strlen($cid) > 0) {
					if(is_bool($value = $this->getDisplay($cid))) {
						$display = $value;
					}
				}
				// 区域原文;
				$original = $matches[4][$k];
				// 最终判断;
				$str = str_replace($matches[0][$k], ($display) ? $original : '', $str);
			}
		}
	}

	/**
	 * 解析字符串的传参
	 *
	 * @author HanskiJay
	 * @since  2021-05-29
	 * @param  string      $str     待解析的字符串
	 * @param  string      &$result
	 * @return boolean
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
	 * 替换绑定变量
	 *
	 * @author HanskiJay
	 * @since  2021-05-29
	 * @param  string                     $key   变量名
	 * @param  int|integer|string|boolean $value 变量值
	 * @param  string                     &$str  原始字符串
	 * @return void
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
	 * 渲染视图到前端
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @return string
	 */
	protected function render() : string
	{
		// 获取模板;
		$this->init();

		/* 开始解析模板组件 */
		$regex = '/{require (.*)}/imU';
		while(preg_match_all($regex, $this->viewTemplate, $matches) && (count($matches[1]) > 0)) {
			foreach($matches[1] as $key => $path) {
				$path = $this->getViewPath($path);
				Helper::escapeSlash($path);
				$this->viewTemplate = str_replace($matches[0][$key], is_file($path) ? file_get_contents($path) : "Template {$path} Not Found", $this->viewTemplate);
			}
		}
		// 转换常量绑定;
		if(preg_match_all('/{([0-9A-Z_]*)}/mU', $this->viewTemplate, $matches)) {
			foreach($matches[1] as $k => $constName) {
				if(defined($constName) || isset($this->constants[$constName])) {
					$this->viewTemplate = str_replace($matches[0][$k], @constant($constName) ?? $this->constants[$constName], $this->viewTemplate);
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

		// 解析@display用法;
		$this->parseDisplayArea($this->viewTemplate);
		// 绑定资源路径到路由;
		$this->parseResourcePath($this->viewTemplate);

		return $this->viewTemplate;
	}

	/**
	 * 生成静态资源路由地址
	 *
	 * @author HanskiJay
	 * @since  2021-05-29
	 * @param  string            $filePath 静态资源文件路径
	 * @return string
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
	 * 静态资源相对/绝对路径获取方法
	 */
	/**
	 * 获取组件资源
	 *
	 * @return string
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 */
	public function getComponent(string $index, int $mode = 0) : string
	{
		$path = $this->getViewPath('component') . DIRECTORY_SEPARATOR . Helper::escapeSlash($index);
		return ($mode === 0) ? $path : (is_file($path) ? file_get_contents($path) : "[VIEW-COMPONENT] File '{$path}' Not Found");
	}

	/**
	 * 获取CSS文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getCssPath(string $index) : string
	{
		return $this->getStaticPath('css') . Helper::escapeSlash($index);
	}

	/**
	 * 获取公共目录下的CSS文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getPublicCssPath(string $index) : string
	{
		return $this->getResourcePath('css') . Helper::escapeSlash($index);
	}

	/**
	 * 获取JS文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getJsPath(string $index) : string
	{
		return $this->getStaticPath('js') . Helper::escapeSlash($index);
	}

	/**
	 * 获取公共目录下的JS文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getPublicJsPath(string $index) : string
	{
		return $this->getResourcePath('js') . Helper::escapeSlash($index);
	}

	/**
	 * 获取IMG文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getImgPath(string $index) : string
	{
		return $this->getStaticPath('img') . Helper::escapeSlash($index);
	}

	/**
	 * 获取公共目录下的IMG文件目录的指定文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index 文件/文件夹索引
	 * @return string
	 */
	public function getPublicImgPath(string $index) : string
	{
		return $this->getResourcePath('img') . Helper::escapeSlash($index);
	}

	/**
	 * 判断是否存在一个局部静态资源文件目录
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $index1 文件夹索引
	 * @param  string      $index2 文件索引
	 * @return boolean
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
	 * 返回模板
	 *
	 * @author HanskiJay
	 * @since  2021-05-29
	 * @return null|string
	 */
	protected function &getTemplate() : ?string
	{
		return $this->viewTemplate;
	}
}