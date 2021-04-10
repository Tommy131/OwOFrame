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
	* GitHub: https://github.com/Tommy131
	
************************************************************************/

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
	$prefix = '[ConfigParser] 配置文件加载失败:';
	$config = [];
	if(!file_exists($file)) return [];
	$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$currentGroup = null;
	foreach($content as $line) {
		# ---* [识别组开始标签] *---
		if(preg_match("/^\[(.*)\]$/", $line, $match)) {
			$group = trim($match[1]);
			if(!isset($config[$group])) {
				$currentGroup   = $group;
				$config[$group] = [];
			} else {
				throw error("{$prefix}已存在组 '{$group}' !");
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
	global $_global;
	static $groupName;
	if($_global === null) {
		$file = FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR . 'global.ini';
		if(!file_exists($file)) return $default;
		$_global = loadConfig($file);	
	}

	$array = explode('@', $str);
	if((count($array) === 2) && (isset($_global[$array[0]]))) {
		if($array[0] !== $groupName) $groupName = $array[0];
		$str = $array[1];
	}
	unset($array);

	if(!isset($groupName) && isset($_global[$str])) {
		$groupName = $str;
		return '成功设置组名, 现可以查看子变量';
	}

	return $_global[$groupName][$str] ?? $default;
}

