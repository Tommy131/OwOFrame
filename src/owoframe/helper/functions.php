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
use owoframe\constant\BasicConstant as BC;
use owoframe\exception\OwOFrameException;
use owoframe\utils\LogWriter;

if(!defined('owohttp')) define('owohttp', 'owosuperget');

/**
 * HTTP基础方法
 */
/**
 * $_SERVER 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function server(string $index, bool $autoUpper = true)
{
	if(strtolower($index) === owohttp) return $_SERVER;
	if($autoUpper) $index = strtoupper($index);
	return $_SERVER[$index] ?? null;
}

/**
 * $_SESSION 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string $index
 * @param  string $default
 * @return mixed
 */
function session(string $index, $default = '') {
	if(strtolower($index) === owohttp) {
		return $_SESSION ?? [];
	}
	return $_SESSION[$index] ?? $default;
}

/**
 * $_GET 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function get(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_GET;
	// return $_GET[$index] ?? null;
	if(isset($_GET['s'])) unset($_GET['s']);
	return (strtolower($index) === owohttp) ? ($_GET ?? null) : ($_GET[$index] ?? null);
}

/**
 * $_POST 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function post(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_POST;
	// return $_POST[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_POST ?? null) : ($_POST[$index] ?? null);
}

/**
 * $_PUT 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function put(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_PUT;
	// return $_PUT[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_PUT ?? null) : ($_PUT[$index] ?? null);
}

/**
 * $_FILES 的简化版本
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function files(string $index, bool $autoUpper = false)
{
	if($autoUpper) $index = strtoupper($index);
	// if(strtolower($index) === owohttp) return $_FILES;
	// return $_FILES[$index] ?? null;
	return (strtolower($index) === owohttp) ? ($_FILES ?? null) : ($_FILES[$index] ?? null);
}

/**
 * 检查所给出的索引存在于哪一个请求模式数据中
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string  $index
 * @param  boolean $autoUpper
 * @return mixed
 */
function check(string $index, bool $autoUpper = false, &$method = 'NULL')
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

/**
 * 通过 php://input 获取 HTTP_RAW_DATA
 *
 * * 修复了对前端使用fetch等方法时PHP无法取到数据的情况
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @return null|string
 */
function fetch() : ?string
{
	return file_get_contents('php://input') ?? null;
}

/**
 * 返回请求模式的整型代码
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @return integer
 */
function requestMode() : int
{
	$httpMode = strtolower($_SERVER['REQUEST_METHOD']);
	$ajaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

	if($ajaxMode) {
		if($httpMode === 'get') {
			return BC::AJAX_P_GET_MODE;
		}
		elseif($httpMode === 'post') {
			return BC::AJAX_P_POST_MODE;
		}
		return BC::AJAX_MODE;
	}
	elseif($httpMode === 'get') {
		return BC::GET_MODE;
	}
	elseif($httpMode === 'post') {
		return BC::POST_MODE;
	}
	elseif($httpMode === 'put') {
		return BC::PUT_MODE;
	} else {
		return -1;
	}
}


/**
 * 系统基本方法
 */
/**
 * 如果存在则获取数组中的元素值否之返回默认设定值
 *
 * @author HanskiJay
 * @since  2021-01-10
 * @param  array       array   所需数组
 * @param  string      key     搜索的键名
 * @param  mixed       default 默认返回值
 * @return mixed
 */
function arrayGet(array $array, string $key, $default = '')
{
	return $array[$key] ?? $default;
}

/**
 * 比较两个参数的类型是否相等
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  mixed      $p1     参数1
 * @param  mixed      $p2     参数2
 * @param  mixed      &$types 两个参数的类型数组
 * @return boolean
 */
function compareType($p1, $p2, &$types = []) : bool
{
	$type1 = gettype($p1);
	$type2 = gettype($p2);
	$types = [$type1, $type2];
	return $type1 === $type2;
}

/**
 * 检查目标数组是否缺少某个元素(仅限二维数组)
 *
 * @author HanskiJay
 * @since  2021-01-10
 * @param  array       $data      需要检查的数组
 * @param  array       $needle    需要检查的键名
 * @param  string      $missParam 返回缺少的参数
 * @return bool
 */
function checkArrayValid(array $data, array $needle, &$missParam = null) : bool
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
 * 判断传入的数据是否已序列化
 *
 * @author HanskiJay
 * @since  2021-01-31
 * @param  string      $data 需要判断的数据
 * @return boolean
 */
function is_serialized(string $data)
{
	$data = trim($data);
	if('N;' == $data) return true;
	if(!preg_match('/^([adObis]):/', $data, $matches)) return false;
	switch ($matches[1]) {
		case 'a':
		case 'O':
		case 's':
		if(preg_match("/^{$matches[1]}:[0-9]+:.*[;}]\$/s", $data)) return true;
		break;
		case 'b':
		case 'i':
		case 'd':
		if(preg_match("/^{$matches[1]}:[0-9.E-]+;\$/", $data)) return true;
		break;
	}
	return false;
}

/**
 * 字符串编码转码UTF-8
 *
 * @author HanskiJay
 * @since  2021-01-31
 * @param  string      $str 需要转码的字符串
 * @return string
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
 * 更改变量类型
 *
 * @author HanskiJay
 * @since  2021-01-31
 * @param  string $var
 * @param  mixed $done
 * @return void
 */
function changeType(string $var, &$done) : void
{
	if(is_numeric($var)) {
		$done = preg_match('/^[0-9]+\.[0-9]+$/', $var) ? (float) $var : (int) $var;
	} else {
		$tmp_var = (strtolower($var) === 'true') ? true : ((strtolower($var) === 'false') ? false : $var);
		$done = ($tmp_var === $var) ? ((strtolower($var) === 'null') ? null : $tmp_var) : $tmp_var;
	}
}

/**
 * 将布尔值转换为字符串
 *
 * @author HanskiJay
 * @since  2021-01-31
 * @param  null|boolean $bool
 * @param  mixed        $done
 * @return void
 */
function changeBool2String(?bool $bool, &$done) : void
{
	$done = $bool ? 'true' : 'false';
}

/**
 * 系统特殊方法
 */
/**
 * 创建一个简单的错误信息
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  int         $code    状态码
 * @param  string      $message 错误信息
 * @return OwOFrameException
 */
function error(string $message, int $code = 0) : OwOFrameException
{
	return new class($code, $message) extends OwOFrameException implements BC
	{
		public function __construct(int $code, ?string $message)
		{
			$this->code    = $code;
			$this->message = $message ?? 'unknown';
		}
	};
}

/**
 * 用作在CMD & SHELL下获取标准输入的方法
 *
 * @author HanskiJay
 * @since  2021-03-06
 * @param  string      $output  向CMD & SHELL输出的显示文字
 * @param  mixed       $default 默认结果
 * @return STDIN       标准输入|默认结果(当标准输入结果为空时)
 */
function ask(string $output, $default = null, string $logLevel = 'info')
{
	LogWriter::{$logLevel}($output . (!is_null($default) ? "[Default: {$default}]" : ''));
	return trim(fgets(STDIN) ?? $default);
}
