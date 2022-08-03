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
namespace owoframe\utils;

use JsonSerializable;

use owoframe\constants\StandardOutputConstant;

class DataEncoder implements JsonSerializable, StandardOutputConstant
{
	/**
	 * 原始数据
	 *
	 * @access protected
	 * @var array
	 */
	protected $originData = [];

	/**
	 * 最终输出数据
	 *
	 * @access protected
	 * @var string
	 */
	protected $output = '';



	public function __construct(array $data = [])
	{
		$this->setData($data);
	}

	/**
	 * 设置原始数据
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  array      $data 原始数据
	 * @return DataEncoder
	 */
	public function setData(array $data) : DataEncoder
	{
		$this->originData = $data;
		return $this;
	}

	/**
	 * 以键名方式添加数据
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  mixed      $key 键名
	 * @param  mixed       $val 键值
	 * @return DataEncoder
	 */
	public function setIndex($key, $val) : DataEncoder
	{
		$this->originData[$key] = $val;
		return $this;
	}

	/**
	 * 合并自定义输出信息到全集
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  array      $array 新的数据数组
	 * @return DataEncoder
	 */
	public function mergeData(array $array) : DataEncoder
	{
		$this->originData = array_merge($this->originData, $array);
		return $this;
	}

	/**
	 * 设置标准信息并且自动返回实例(此方法将会清空原本存在的数据)
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  int      $code       状态码
	 * @param  string   $msg        返回信息
	 * @param  bool     $result     执行结果
	 * @return DataEncoder
	 */
	public function setStandardData(int $code, string $msg, bool $result) : DataEncoder
	{
		return $this->reset()
			 ->setIndex('code',   $code)
			 ->setIndex('msg',    $msg)
			 ->setIndex('result', $result)
			 ->setIndex('time',   date("Y-m-d H:i:s"));
	}

	/**
	 * 使用JSON编码数据格式
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @return string
	 */
	public function encode() : string
	{
		return $this->output = json_encode($this, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 解码JSON数据格式
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @return array
	 */
	public function decode() : array
	{
		return json_decode($this->output, true);
	}

	/**
	 * 返回查找的键名的值
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  mixed      $key     键名
	 * @param  mixed      $default 默认返回值
	 * @return mixed
	 */
	public function getIndex($key, $default = '')
	{
		return $this->originData[$key] ?? $default;
	}

	/**
	 * 获取原始数据
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @return array
	 */
	public function getOriginData() : array
	{
		return $this->originData;
	}

	/**
	 * 获取输出数据
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @return string
	 */
	public function getOutput() : string
	{
		return $this->output;
	}

	/**
	 * 重置数据
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @return DataEncoder
	 */
	public function reset() : DataEncoder
	{
		$this->originData = [];
		$this->output     = '';
		return $this;
	}

	/**
	 * JsonSerializable接口规定方法
	 *
	 * @author HanskiJay
	 * @since  2021-03-21
	 * @return mixed
	 */
	public function jsonSerialize()
	{
		return $this->originData;
	}

	/**
	 * 判断传入的字符串是否仅为字母和数字
	 *
	 * @author HanskiJay
	 * @since  2021-02-11
	 * @param  string      $str   传入的字符串
	 * @param  &match      $match 匹配结果
	 * @return boolean
	 */
	public static function isOnlyLettersAndNumbers(string $str, &$match = null) : bool
	{
		return (bool) preg_match("/^[A-Za-z0-9]+$/", $str, $match);
	}
}