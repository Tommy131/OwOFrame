<?php

if(!defined('owohttp')) define('owohttp', 'owosuperget');

/**
 * HTTP基础处理;
*/

function server(string $index, bool $autoUpper = true)
{
	if($autoUpper) $index = strtoupper($index);
	if(strtolower($index) === owohttp) return $_SERVER;
	return $_SERVER[$index] ?? null;
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


/**
 * @Session GET
*/
function session(string $index, $default = '') {
	if(strtolower($index) === owohttp) {
		return $_SESSION ?? [];
	}
	return $_SESSION[$index] ?? $default;
}


/**
 * Status Code Exception Closure Function
*/
function createStatus(int $code, ?string $message)
{
	return new class($code, $message) extends \backend\system\exception\OwOFrameException implements \backend\system\code\CodeBase
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
 * @method      throwError
 * @description 抛出异常消息到前端 | throw error message to HTML;
 * @author      HanskiJay
 * @doenIn      2021-01-09
 * @param       string[errorMsg|错误消息]
 * @param       string[file|文件名]
 * @param       int[line|行数]
 * @return      void
 */
function throwError(string $errorMsg, string $file, int $line) : void
{
	$msg  = '[OwOError] An Error caused by following file: ' . $file . ' at line ' . $line . "<br/>";
	$msg .= 'Error message:' . "<br/>";
	if(function_exists('\OwOBootstrap\writeLogExit')) \OwOBootstrap\writeLogExit($msg . " " . $errorMsg);
}