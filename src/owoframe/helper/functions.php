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
use owoframe\contract\BasicCodes;
use owoframe\exception\OwOFrameException;

if(!defined('owohttp')) define('owohttp', 'owosuperget');

/**
 * HTTP基础方法
*/
function server(string $index, bool $autoUpper = true)
{
	if($autoUpper) $index = strtoupper($index);
	if(strtolower($index) === owohttp) return $_SERVER;
	return $_SERVER[$index] ?? null;
}

function session(string $index, $default = '') {
	if(strtolower($index) === owohttp) {
		return $_SESSION ?? [];
	}
	return $_SESSION[$index] ?? $default;
}

function get(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_GET;
	// return $_GET[$index] ?? null;
	if(isset($_GET['s'])) unset($_GET['s']);
	return (strtolower($index) === owohttp) ? ($_GET ?? null) : ($_GET[$index] ?? null);
}

function post(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_POST;
	// return $_POST[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_POST ?? null) : ($_POST[$index] ?? null);
}

function put(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_PUT;
	// return $_PUT[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_PUT ?? null) : ($_PUT[$index] ?? null);
}

function files(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_FILES;
	// return $_FILES[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_FILES ?? null) : ($_FILES[$index] ?? null);
}

function check(string $index, bool $autoUpper = false, &$method = 'GET')
{
	if($autoUpper) $index = strtoupper($index);
	if(($result = get($index)) !== null) {
		$method = 'GET';
	}
	elseif(($result = post($index)) !== null) {
		$method = 'POST';
	}
	elseif(($result = put($index)) !== null) {
		$method = 'PUT';
	}
	elseif(($result = files($index)) !== null) {
		$method = 'FILE';
	} else {
		$result = false;
	}
	return $result;
}

function requestMode() : int
{
	$httpMode = strtolower(server('REQUEST_METHOD'));
	$ajaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

	if($ajaxMode) {
		if($httpMode === 'get') {
			return BasicCodes::AJAX_P_GET_MODE;
		}
		elseif($httpMode === 'post') {
			return BasicCodes::AJAX_P_POST_MODE;
		}
		return BasicCodes::AJAX_MODE;
	}
	elseif($httpMode === 'get') {
		return BasicCodes::GET_MODE;
	}
	elseif($httpMode === 'post') {
		return BasicCodes::POST_MODE;
	}
	elseif($httpMode === 'put') {
		return BasicCodes::PUT_MODE;
	} else {
		return -1;
	}
}


/**
 * 系统基本方法
 */
/**
 * @method      arrayGet
 * @description description
 * @author      HanskiJay
 * @doenIn      2021-01-10
 * @param       array[array|所需数组]
 * @param       string[key|搜索的键名]
 * @param       mixed[default|默认返回值]
 * @return      mixed
 */
function arrayGet(array $array, string $key, $default = '')
{
	return $array[$key] ?? $default;
}

/**
 * @method      compareType
 * @description 比较两个参数的类型是否相等
 * @author      HanskiJay
 * @doenIn      2021-03-06
 * @param       mixed      $p1     参数1
 * @param       mixed      $p2     参数2
 * @param       mixed      &$types 两个参数的类型数组
 * @return      boolean
 */
function compareType($p1, $p2, &$types = []) : bool
{
	$type1 = gettype($p1);
	$type2 = gettype($p2);
	$types = [$type1, $type2];
	return $type1 === $type2;
}

/**
 * @method      checkArrayValid
 * @description 检查目标数组是否缺少某个元素(仅限二维数组)
 * @author      HanskiJay
 * @doenIn      2021-01-10
 * @param       array[data|需要检查的数组]
 * @param       array[needle|需要检查的键名]
 * @param       string[missParam|返回缺少的参数]
 * @return      bool
 */
function checkArrayValid(array $data, array $needle, ?string &$missParam = "") : bool
{
	$data = array_filter($data);
	$result = false;
	while(count($needle) > 0)
	{
		$temp = array_shift($needle);
		if(!isset($data[$temp]))
		{
			$missParam = $temp;
			$result = false;
			break;
			return $result;
		}
		else $result = true;
	}
	return $result;
}

/**
 * @method      is_serialized
 * @description 判断传入的数据是否已序列化
 * @author      HanskiJay
 * @doenIn      2021-01-31
 * @param       string      $data 需要判断的数据
 * @return      boolean
 */
function is_serialized(string $data)
{
	$data = trim($data);
	if('N;' == $data) return true;
	if(!preg_match('/^([adObis]):/', $data, $badions)) return false;
	switch ($badions[1]) {
		case'a':
		case'O':
		case's':
		if(preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) return true;
		break;
		case'b':
		case'i':
		case'd':
		if(preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) return true;
		break; 
	}
	return false;
}

/**
 * @method      str2UTF8
 * @description 字符串编码转码UTF-8
 * @author      HanskiJay
 * @doenIn      2021-01-31
 * @param       string      $str 需要转码的字符串
 * @return      string
 */
function str2UTF8(string $str) : string
{
	if(defined('MB_SUPPORTED') && MB_SUPPORTED) {
		$encode = mb_detect_encoding($str, ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"]);
		return ($encode === "UTF-8") ? $str : mb_convert_encoding($str, "UTF-8", $encode);
	} else {
		return $str;
	}
}


/**
 * 系统特殊方法
 */
/**
 * @method      error
 * @description 创建一个简单的错误信息
 * @author      HanskiJay
 * @doenIn      2021-03-06
 * @param       int         $code    状态码
 * @param       string      $message 错误信息
 * @return      object@OwOFrameException
 */
function error(string $message, int $code = 0) : OwOFrameException
{
	return new class($code, $message) extends OwOFrameException implements BasicCodes
	{
		public function __construct(int $code, ?string $message)
		{
			$this->code    = $code;
			$this->message = $message ?? 'unknown';
		}

		public function getRealFile() : string
		{
			return $this->getTrace()[1]['file'] ?? $this->getTrace()[0]['file'];
		}

		public function getRealLine() : int
		{
			return $this->getTrace()[1]['line'] ?? $this->getTrace()[0]['line'];
		}

		public function getMethod() : string
		{
			return $this->getTrace()[1]['function'] ?? $this->getTrace()[0]['function'];
		}
	};
}

/**
 * @method      ask
 * @description 用作在CMD & SHELL下获取标准输入的方法
 * @author      HanskiJay
 * @doenIn      2021-03-06
 * @param       string      $output  向CMD & SHELL输出的显示文字
 * @param       mixed       $default 默认结果
 * @return      STDIN                标准输入|默认结果(当标准输入结果为空时)
 */
function ask(string $output, $default = null)
{
	logger($output . (!is_null($default) ? "[Default: {$default}]" : ''));
	return trim(fgets(STDIN) ?? $default);
}
