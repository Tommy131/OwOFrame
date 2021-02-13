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

declare(strict_types=1);
namespace backend\system\utils;

class DataEncoder implements \JsonSerializable
{
	private static $data = "";

	
	/**
	 * @method      setData
	 * @description 设置原始数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       array[data|原始数据]
	 * @return      void
	 */
	public static function setData(array $data) : void
	{
		self::$data = $data;
	}

	/**
	 * @method      encode
	 * @description 使用JSON编码数据格式
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @return      string
	 */
	public static function encode() : string
	{
		self::$data = json_encode(new self, JSON_UNESCAPED_UNICODE);
		return self::$data;
	}

	public function jsonSerialize()
	{
		return self::$data;
	}

	/**
	 * @method      decode
	 * @description 解码JSON数据格式
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @return      void
	 */
	public static function decode() : void
	{
		self::$data = json_decode(self::$data);
	}

	/**
	 * @method      reset
	 * @description 重置原始数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @return      void
	 */
	public static function reset() : void
	{
		self::$data = [];
	}

	/**
	 * @method      getIndex
	 * @description 获取一设置的键名的值
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       array[key|键名]
	 * @param       array[default|默认返回值(Default: null)]
	 * @return      mixed
	 */
	public static function getIndex(string $key)
	{
		return self::$data[$key] ?? "";
	}

	/**
	 * @method      getAll
	 * @description 获取所有信息
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @return      array
	 */
	public static function getAll() : array
	{
		if(!is_array(self::$data)) self::$data = json_decode(self::$data, true);
		if(!is_array(self::$data)) self::$data = (array) self::$data;
		return self::$data;
	}

	/**
	 * @method      setIndex
	 * @description 以键名方式添加数据
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       string[key|键名]
	 * @param       mixed[val|键值]
	 * @return      void
	 */
	public static function setIndex(string $key, $val) : void
	{
		self::$data[$key] = $val;
	}

	/**
	 * @method      mergeArr
	 * @description 合并自定义输出信息到全集
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       array[array|新的数据数组]
	 * @param       array[ec|重试值?(我也不知道为啥会写一个这个东西, 先留着吧)]
	 * @return      void
	 */
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

	/**
	 * @method      setStandardData
	 * @description 设置标准信息并且自动编码
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       int[code|状态码]
	 * @param       bool[result|执行结果]
	 * @param       string[msg|返回信息]
	 * @param       bool[autoReturn|自动编码且返回编码后的信息串(Default: true)]
	 * @return      null|string
	 */
	public static function setStandardData(int $code, bool $result, string $msg, bool $autoReturn = true) : ?string
	{
		self::reset();
		self::setIndex("code",   $code);
		self::setIndex("msg",    $msg);
		self::setIndex("result", $result);
		self::setIndex("time",   date("Y-m-d H:i:s"));

		return $autoReturn ? self::encode() : null;
	}

	/**
	 * @method      isOnlyLettersAndNumbers
	 * @description 判断传入的字符串是否仅为字母和数字
	 * @author      HanskiJay
	 * @doenIn      2021-02-11
	 * @param       string[str|传入的字符串]
	 * @param       &match[匹配结果]
	 * @return      boolean
	 */
	public static function isOnlyLettersAndNumbers(string $str, &$match) : bool
	{
		return (bool) preg_match("/^[A-Za-z0-9]+$/", $str, $match);
	}
}