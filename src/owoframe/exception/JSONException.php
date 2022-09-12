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
namespace owoframe\exception;

use JsonSerializable;
use owoframe\utils\DataEncoder;

class JSONException extends OwOFrameException implements JsonSerializable
{
    /**
     * 响应载体
     *
     * @var DataEncoder
     */
    protected $payload;

    public function __construct(array $messages = [])
    {
        $this->message = $messages;
        $this->payload = new DataEncoder($messages);
        header('Content-Type: application/json; charset=UTF-8');
        die($this->payload->encode());
    }

    public function getPayload() : DataEncoder
    {
        return $this->payload;
    }

    public function jsonSerialize()
    {
        return $this->message;
    }
}