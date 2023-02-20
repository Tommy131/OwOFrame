<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-02 17:12:36
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 19:26:49
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\utils;



use JsonSerializable;

class DataEncoder implements JsonSerializable
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
     * @param  array $data 原始数据
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
     * @param  mixed $key 键名
     * @param  mixed $val 键值
     * @return DataEncoder
     */
    public function setIndex($key, $val) : DataEncoder
    {
        $this->originData[$key] = $val;
        return $this;
    }

    /**
     * 通过键名删除一则数据
     *
     * @param  mixed $key
     * @return DataEncoder
     */
    public function unsetIndex($key) : DataEncoder
    {
        if(isset($this->originData[$key])) {
            unset($this->originData[$key]);
        }
        return $this;
    }

    /**
     * 合并自定义输出信息到全集
     *
     * @param  array $data 新的数据数组
     * @return DataEncoder
     */
    public function mergeData(array $data) : DataEncoder
    {
        # @see https://www.php.net/manual/zh/function.array-merge.php
        $this->originData = array_merge($this->originData, $data);
        return $this;
    }

    /**
     * 设置标准信息并且自动返回实例(此方法将会清空原本存在的数据)
     *
     * @param  int    $code      状态码
     * @param  string $message   返回信息
     * @param  bool   $microtime 使用时间戳
     * @return DataEncoder
     */
    public function setStandardData(int $code, string $message, bool $microtime = false) : DataEncoder
    {
        return $this->reset()->setData([
            'code'    => $code,
            'message' => $message,
            'time'    => $microtime ? microtime() : date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 使用JSON编码数据格式
     *
     * @return string
     */
    public function encode() : string
    {
        return $this->output = json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 解码JSON数据格式
     *
     * @param  array $reload 将解码的数据覆盖到原始数据
     * @return array
     */
    public function decode(bool $reload = false) : array
    {
        $decoded = json_decode($this->output, true);
        if($reload) {
            $this->originData = $decoded;
        }
        return $decoded;
    }

    /**
     * 返回查找的键名的值
     *
     * @param  mixed $key     键名
     * @param  mixed $default 默认返回值
     * @return mixed
     */
    public function getIndex($key, $default = null)
    {
        return $this->originData[$key] ?? $default;
    }

    /**
     * 获取原始数据
     *
     * @return array
     */
    public function getOriginData() : array
    {
        return $this->originData;
    }

    /**
     * 获取输出数据
     *
     * @return string
     */
    public function getOutput() : string
    {
        return $this->output;
    }

    /**
     * 重置数据
     *
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
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->originData;
    }

    /**
     * 魔术方法
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->setIndex($name, $value);
    }

    /**
     * 魔术方法
     *
     * @param mixed $nam
     */
    public function __unset($name)
    {
        $this->unsetIndex($name);
    }
}
?>