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
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

namespace backend\system\utils;

use FilesystemIterator as FI;

class Logger
{
	const DEFAULT_LOG_NAME = "owoblog_run.log";
	const LOG_PREFIX       = "[{date}][{time}][{prefix}/{level}] > {msg}";
	private static $fileName;
	public static $maxFileSize = 1024; // mb, 日志文件大小大于这个值时自动截断并且生成新的日志;


	// Func: 设置日志名称;
	public static function setFileName(string $fileName) : void
	{
		self::$fileName = LOG_PATH.$fileName;
	}
	// Func: 写入错误日志;
	public static function writeLog(string $msg, string $prefix = 'OwOWeb', string $level = "INFO")
	{
		if(is_null(self::$fileName)) self::$fileName = LOG_PATH.self::DEFAULT_LOG_NAME;
		$files = iterator_to_array(new FI(LOG_PATH, FI::CURRENT_AS_PATHNAME | FI::SKIP_DOTS), false);
		$ext   = self::getExt();
		foreach($files as $file)
		{
			if(is_file($file) && (substr($file, -strlen($ext)) === $ext) && (strpos($file, self::$fileName) !== false) && (filesize(self::$fileName) >= self::$maxFileSize * 1000))
			{
				rename($file, str_replace($ext, "", $file).date("_Y_m_d").$ext);
			}
		}
		return file_put_contents(self::$fileName, str_replace(["{date}", "{time}", "{prefix}", "{level}", "{msg}"], [date("Y-m-d"), date("H:i:s"), $prefix, $level, $msg.PHP_EOL], self::LOG_PREFIX), FILE_APPEND|LOCK_EX);
	}
	// Func: 删除错误日志;
	public static function cleanLog(string $fileName = \null) : void
	{
		if(is_null($fileName)) $fileName = LOG_PATH.self::$fileName;
		if(is_file(self::$fileName)) unlink(self::$fileName);
	}
	// Func: 获取文件名后缀;
	public static function getExt() : string
	{
		return ".".(@end(explode(".", self::$fileName)) ?? "");
	}
}
?>