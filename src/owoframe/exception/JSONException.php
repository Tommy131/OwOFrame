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
 * @Date         : 2023-02-09 19:16:33
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 19:21:59
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */

declare(strict_types=1);
namespace owoframe\exception;



use JsonSerializable;
use owoframe\utils\DataEncoder;

class JSONException extends OwOFrameException implements JsonSerializable
{
    /**
     * 原始数据
     *
     * @var array
     */
    protected $data;

    /**
     * 响应载体
     *
     * @var DataEncoder
     */
    protected $payload;

    /**
     * 输出缓冲
     *
     * @var string
     */
    protected $buffer;


    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->payload = new DataEncoder($data);
        header('Content-Type: application/json; charset=UTF-8');
    }

    /**
     * 返回响应载体
     *
     * @return DataEncoder
     */
    public function getPayload() : DataEncoder
    {
        return $this->payload;
    }

    /**
     * 编码数据数组
     *
     * @param  boolean $update
     * @return string
     */
    public function encode(bool $update = false) : string
    {
        if($update || !$this->buffer) {
            $this->buffer = $this->payload->encode();
        }
        return $this->buffer;
    }

    /**
     * 返回原始数据
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}