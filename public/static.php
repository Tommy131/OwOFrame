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
	* 静态资源回调路由

**********************************************************************/

if(!defined('START_MICROTIME'))  define('START_MICROTIME',  microtime(true));
// 开发者模式 | DEBUG_MODE;
if(!defined('DEBUG_MODE'))       define('DEBUG_MODE', false);
// 记录错误日志 | LOG_ERROR;
if(!defined('LOG_ERROR'))        define('LOG_ERROR', false);
if(!defined('DEFAULT_APP_NAME')) define('DEFAULT_APP_NAME', '');
if(!defined('DENY_APP_LIST'))    define('DENY_APP_LIST', []);
// 默认时区 | Default timezone;
if(!defined('TIME_ZONE'))        define('TIME_ZONE', 'Europe/Berlin');

// 引入自动加载文件 | require autoload file;
$classLoader = require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
use owoframe\helper\Helper;
use owoframe\http\HttpManager as HTTP;
use owoframe\http\route\Router;
use owoframe\utils\Logger;

$master = new owoframe\MasterManager($classLoader);
$http = $master->getManager('http');
$http->start(false);

Logger::$logPrefix = 'static.php';
$parser = Router::getParameters(-1);

if(count($parser) === 2) {
	$type    = array_shift($parser);
	$hashTag = @array_shift(explode('.', array_shift($parser)));
	$file    = F_CACHE_PATH . $type . DIRECTORY_SEPARATOR . $hashTag . '.php';
	if(is_file($file)) {
		$tempData = require_once($file);
		if(is_array($tempData)) {
			if(isset($tempData['expireTime'])) {
				if(microtime(true) - $tempData['expireTime'] <= 0) {
					$reason = 'FILE ALLOWED ACCESS TIME HAS EXPIRED';
					Logger::info('[403@Access Denied=' . $reason . '] ' . Helper::getClientIp() . ' -> ' . HTTP::getCompleteUrl());
					stdDie($reason, '', 403);
				}
			}
			if(isset($tempData['callback']) && is_callable($tempData['callback'])) {
				$tempData['callback']();
			}
		}
	} else {
		Logger::info('[404@Not Found] ' . Helper::getClientIp() . ' -> ' . HTTP::getCompleteUrl());
		stdDie('REQUESTED FILE NOT FOUND', '<p>FILE: ' . $hashTag . '.' . $type . '</p>');
	}
} else {
	Logger::info('[403@Access Denied] ' . Helper::getClientIp() . ' -> ' . HTTP::getCompleteUrl());
	stdDie('', '', 403);
}
$master->stop();


/**
 * 标准结束脚本输出
 *
 * @author HanskiJay
 * @since  2021-03-14
 * @param  string      $title     输出标题(为空则不输出任何结果)
 * @param  string      $customMsg 自定义输出信息
 * @param  int|integer $code      HTTP响应状态码
 * @return void
 */
function stdDie(string $title, string $customMsg = '', int $code = 404) : void
{
	owoframe\http\HttpManager::setStatusCode($code);
	if(($title !== '') && ($title !== null)) {
		die(
			'<div style="weight: 100%; text-align: center">'.
				'<p>[HTTP_ERROR@' . $code . ']</p>'.
				'<p style="font-weight: 600">---------- ' . $title . ' ----------</p>'.
				$customMsg .
				'<p>CLIENT:     ' . Helper::getClientIp() . '</p>'.
				'<p>User-Agent: ' . server('HTTP_USER_AGENT') . '</p>'.
				'<p style="font-weight: 600">---------------------------------------------------------</p>'.
				'<p>[' . date('Y-m-d H:i:s') . ']</p>'.
			'</div>'
		);
	}
}