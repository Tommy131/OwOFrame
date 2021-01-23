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

declare(strict_types=1);
namespace backend\system\utils;

class DataEncoder
{
	private static $data = "";

	
	// Func: 设置原始数据;
	public static function setData(array $data) : void
	{
		self::$data = $data;
	}
	// Func: 使用JSON编码数据格式;
	public static function encode() : string
	{
		self::$data = json_encode(self::$data, JSON_UNESCAPED_UNICODE);
		return self::$data;
	}
	// Func: 解码JSON数据格式;
	public static function decode() : void
	{
		self::$data = json_decode(self::$data);
	}
	// Func: 重置原始数据;
	public static function reset() : void
	{
		self::$data = [];
	}
	// Func: 获取一设置的键名的值;
	public static function getIndex(string $key)
	{
		return self::$data[$key] ?? "";
	}
	// Func: 获取所有信息;
	public static function getAll() : array
	{
		if(!is_array(self::$data)) self::$data = json_decode(self::$data, true);
		if(!is_array(self::$data)) self::$data = (array) self::$data;
		return self::$data;
	}
	// Func: 以键名方式添加数据;
	public static function setIndex(string $key, $val) : void
	{
		self::$data[$key] = $val;
	}
	// Func: 合并自定义输出信息到全集;
	public static function mergeArr(array $arr, int &$ec = 0) : void
	{
		if(is_array(self::$data)) self::$data = array_merge(self::$data, $arr);
		else {
			if($ec >= 3) return;
			++$ec;
			self::decode();
			self::mergeArr($arr, $ec);
		}
	}
	// Func: 设置标准信息并且自动编码;
	public static function setStandardData(int $code, bool $result, string $msg, bool $autoReturn = true) : ?string
	{
		self::reset();
		self::setIndex("code",   $code);
		self::setIndex("msg",    $msg);
		self::setIndex("result", $result);
		self::setIndex("time",   date("Y-m-d H:i:s"));

		return $autoReturn ? self::encode() : null;
	}
}