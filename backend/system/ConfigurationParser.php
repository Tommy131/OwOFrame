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

$prefix = '[ConfigParser] 全局配置文件加载失败:';
/**
 * @method      loadConfig
 * @description 加载配置文件
 * @author      HanskiJay
 * @doenIn      2021-01-09
 * @param       string[file|文件路径]
 * @return      array
 */
function loadConfig(string $file) : array
{
	global $prefix;
	$config = [];
	if(!file_exists($file)) return [];
	$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(count($content) >= CFG_MAX_LIMIT_LINES) {
		throwError("{$prefix}配置文件 '{$file}' 已超过最大可读取行数(".count($content)."/".CFG_MAX_LIMIT_LINES."), 若需继续执行, 请修改基础配置文件!", __FILE__, __LINE__);
	}
	$currentGroup = '';
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
		if(preg_match("/^([a-zA-Z0-9_\-\.]+)\s?=\s?([a-zA-Z0-9_\-\.]+)$/", $line, $match) && isset($match[1], $match[2])) {
			$match[2] = preg_match("/null/i", $match[2]) ? '' : trim($match[2]);
			$config[$currentGroup][trim($match[1])] = $match[2];
		}
	}
	return $config;
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
			if(!defined("GLOBAL_CONFIG")) define("GLOBAL_CONFIG", loadConfig(CONFIG_PATH . 'global.config'));
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

