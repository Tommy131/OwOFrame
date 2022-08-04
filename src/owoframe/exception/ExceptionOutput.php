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
namespace owoframe\exception;

use Throwable;
use owoframe\application\View;

use owoframe\MasterManager as Master;
use owoframe\helper\Helper;
use owoframe\http\HttpManager as Http;
use owoframe\http\Response;
use owoframe\object\INI;

class ExceptionOutput
{

	public static function debugTrace2String() : string
	{
		$template = "#%d %s(%d): %s%s%s(%s)\n";
		$trace  = debug_backtrace();
		array_shift($trace); // 移除调用本函数的栈追踪;
		$temp   = array_shift($trace);
		$output = sprintf($template, 0, $temp['file'], $temp['line'], $trace[0]['class'], $trace[0]['type'], $trace[0]['function'], @implode(', ', $trace[0]['args']));
		unset($trace[0], $temp);

		foreach($trace as $k => $d) {
			$output .= sprintf($template, $k, $d['file'], $d['line'], $d['class'] ?? '', $d['type'] ?? '', $d['function'] ?? '', @implode(', ', $d['args']));
		}
		return $output . "{main}\n";
	}

	/**
	 * 错误处理方法
	 *
	 * @return void
	 */
	public static function ErrorHandler(int $errorLevel, string $errorMessage, string $errorFile, int $errorLine)
	{
		if(error_reporting() === 0) return false;
		$errorConversion =
		[
			E_ERROR             => 'E_ERROR',
			E_WARNING           => 'E_WARNING',
			E_PARSE             => 'E_PARSE',
			E_NOTICE            => 'E_NOTICE',
			E_CORE_ERROR        => 'E_CORE_ERROR',
			E_CORE_WARNING      => 'E_CORE_WARNING',
			E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
			E_USER_ERROR        => 'E_USER_ERROR',
			E_USER_WARNING      => 'E_USER_WARNING',
			E_USER_NOTICE       => 'E_USER_NOTICE',
			E_STRICT            => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED        => 'E_DEPRECATED',
			E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
		];
		$errorLevel = $errorConversion[$errorLevel] ?? $errorLevel;
		if(($position = strpos($errorMessage, "\n")) !== false) {
			$errorMessage = substr($errorMessage, 0, $position);
		}

		$toString = "{$errorLevel} happened: {$errorMessage} in {$errorFile} at line {$errorLine}";
		$trace    = self::debugTrace2String();
		self::execute([
			'PHP',
			$errorLevel,
			$errorMessage,
			$errorFile,
			$errorLine,
			self::debugTrace2String(),
			$toString . "\nStack trace:\n" . $trace
		]);
	}

	/**
	 * 异常处理方法
	 *
	 * @return void
	 */
	public static function ExceptionHandler(Throwable $exception)
	{
		if($type = $exception instanceof OwOFrameException) {
			$fileName = $exception->getRealFile();
			$realName = $exception->getRealLine();
		} else {
			$fileName = $exception->getFile();
			$realName = $exception->getLine();
		}

		self::execute([
			$type ? 'OwO' : 'PHP',
			Helper::getShortClassName($exception),
			$exception->getMessage(),
			$fileName,
			$realName,
			$exception->getTraceAsString(),
			$exception->__toString()
		]);
	}


	private static function execute(array $args) : void
	{
		if(Helper::isRunningWithCGI()) {
			$view = self::getTemplate();
			$view->assign([
				'type'    => $args[0] ?? 'PHP',
				'subtype' => $args[1] ?? 'None',
				'message' => $args[2] ?? 'None',
				'file'    => $args[3] ?? 'None',
				'line'    => $args[4] ?? 'None',
				'trace'   => $args[5] ?? 'None',
				'runTime' => Master::getRunTime(),
			]);
			$output   = $view->render();
			$response = Http::Response(function() use ($output) {
				return $output;
			}, [], true);
			$response->setResponseCode(502);
			$response->sendResponse();
		}
		self::log($args[6]);
		exit(1);
	}


	/**
	 * 返回OwOView对象
	 *
	 * @return View
	 */
	public static function getTemplate() : View
	{
		$view = new View('ExceptionOutputTemplate', FRAMEWORK_PATH . 'template');
		return $view->assign('debugMode', (INI::_global('owo.debugMode', true)) ? '<span id="debugMode">DebugMode</span>' : '');
	}

	/**
	 * 日志写入
	 *
	 * @param  string $msg
	 * @return void
	 */
	private static function log(string $msg) : void
	{
		$logger   = Master::getInstance()->getUnit('logger');
		$selected = Helper::isRunningWithCGI() ? 'run' : 'cli';
		$logger->createLogger($selected)->updateConfig($selected, [
			'fileName'  => "owoblog_{$selected}_error.log",
			'logPrefix' => 'OwOBlogErrorHandler'
		]);
		$logger->emergency(trim(str2UTF8($msg)));
	}
}
?>