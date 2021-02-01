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
	
************************************************************************/

$prefix = '[ConfigParser] 全局配置文件加载失败:';
/**
 * @method      loadConfig
 * @description 加载配置文件
 * @author      HanskiJay
 * @doenIn      2021-01-09
 * @param       string[file|文件路径]
 * @param       bool[toJson|以JSON对象格式输出(Default: false)]
 * @return      array
 */
function loadConfig(string $file, bool $toJson = false) : array
{
	global $prefix;
	$config = [];
	if(!file_exists($file)) return [];
	$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(!defined('CFG_MAX_LIMIT_LINES')) define('CFG_MAX_LIMIT_LINES', 1000);
	if(count($content) >= CFG_MAX_LIMIT_LINES) {
		throwError("{$prefix}配置文件 '{$file}' 已超过最大可读取行数(".count($content)."/".CFG_MAX_LIMIT_LINES."), 若需继续执行, 请修改基础配置文件!", __FILE__, __LINE__);
	}
	$currentGroup = null;
	foreach($content as $line) {
		# ---* [识别组开始标签] *---
		if(preg_match("/^\[(.*)\]$/", $line, $match)) {
			$group = trim($match[1]);
			if(!isset($config[$group])) {
				$currentGroup   = $group;
				$config[$group] = [];
			} else {
				throwError("{$prefix}已存在组 '{$group}' !", __FILE__, __LINE__);
			}
		}

		# ---* [识别变量定义] *---
		// 先暴力获取一遍, 去除等号两边的空格;
		if(preg_match("/^(.*)=(.*)$/", $line, $match)) {
			$match[1] = trim($match[1]);
			$match[2] = trim($match[2]);
			$line = $match[1] . '=' . $match[2];
		}
		if(preg_match("/^([a-zA-Z0-9_\-\.]+)\s?=\s?([a-zA-Z0-9_\-\.\/\\\ \s@]+)$/", $line, $match) && isset($match[1], $match[2])) {
			$match[2] = preg_match("/null/i", $match[2]) ? '' : trim($match[2]);
			$config[$currentGroup ?? '_'][trim($match[1])] = $match[2];
		}
	}
	// $config = parse_ini_file($file);
	return !$toJson ? $config : json_decode(json_encode($config));
}

/**
 * @method      _global
 * @description 读取全局配置文件 | get global configuration;
 * @author      HanskiJay
 * @doenIn      2021-01-09
 * @param       string[str]
 * @param       mixed[default|默认返回值]
 * @return      mixed
 */
function _global(string $str, $default = null)
{
	if(!defined('GLOBAL_CONFIG')) {
		if(defined('CONFIG_PATH')) {
			if(!defined("GLOBAL_CONFIG")) define("GLOBAL_CONFIG", loadConfig(CONFIG_PATH . 'global.ini'));
		} else {
			return null;
		}
	}
	static $groupName;

	$array = explode('@', $str);
	if((count($array) === 2) && (isset(GLOBAL_CONFIG[$array[0]]))) {
		if($array[0] !== $groupName) $groupName = $array[0];
		$str = $array[1];
	}
	unset($array);

	if(!isset($groupName) && isset(GLOBAL_CONFIG[$str])) {
		$groupName = $str;
		return '成功设置组名, 现可以查看子变量';
	}

	return GLOBAL_CONFIG[$groupName][$str] ?? $default;
}

