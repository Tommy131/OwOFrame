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
namespace owoframe\event\http;

use owoframe\http\HttpManager as Http;

class PageErrorEvent extends \owoframe\event\Event
{
	/* @string 默认模板文件路径 */
	public const DEFAULT_TEMPLATE_FILE = FRAMEWORK_PATH . 'template' . DIRECTORY_SEPARATOR . 'Error.html';


	/**
	 * HTML模板路径
	 *
	 * @var string
	 */
	public static $templateFile = self::DEFAULT_TEMPLATE_FILE;

	/**
	 * 标题
	 *
	 * @var string
	 */
	public static $title = '404 PAGE NOT FOUND';

	/**
	 * 错误响应代码
	 *
	 * @var integer
	 */
	public static $statusCode = 400;

	/**
	 * 输出内容
	 *
	 * @var string
	 */
	public static $output = 'Loun seidon poton dalon queotocy cuca quadosai posidensidy!';

	/**
	 * 模板渲染缓存
	 *
	 * @var string
	 */
	public static $temp;



	public function __construct(array $replaceTags = [], array $replace = [])
	{
		$response = Http::Response([$this, 'call'], [$replaceTags, $replace]);
		$response->setResponseCode(static::$statusCode);
		$response->sendResponse();
	}


	/**
	 * 设置模板文件路径
	 *
	 * @param  string $filePath
	 * @param  string $reset    重置为默认文件路径
	 * @return void
	 */
	public static function setTemplateFile(string $filePath, bool $reset = false) : void
	{
		if(is_file($filePath) && !$reset) {
			static::$templateFile = $filePath;
		} else {
			static::$templateFile = self::DEFAULT_TEMPLATE_FILE;
		}
	}

	/**
	 * 呼叫事件方法
	 *
	 * @param  array   $replaceTags
	 * @param  array   $replace
	 * @param  boolean $update
	 * @return string
	 */
	public function call(array $replaceTags, array $replace, bool $update = false) : string
	{
		if($update || (is_null(static::$temp)))
		{
			$template     = file_get_contents(static::$templateFile);
			$replaceTags  = array_merge(array_filter($replaceTags), ['{title}', '{description}']);
			$replace      = array_merge(array_filter($replace),     [static::$title, static::$output]);
			static::$temp = str_replace($replaceTags, $replace, $template);
		}
		return static::$temp;
	}
}