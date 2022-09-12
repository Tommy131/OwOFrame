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

use owoframe\System;
use owoframe\object\INI;
use owoframe\utils\TextFormat;

class Logger
{
    /**
     * 日志等级 (对应输出的颜色)
     */
    public const LOG_LEVELS =
    [
        'success'   => TextFormat::GREEN,
        'info'      => TextFormat::WHITE,
        'notice'    => TextFormat::AQUA,
        'warning'   => TextFormat::GOLD,
        'alert'     => TextFormat::RED,
        'error'     => TextFormat::LIGHT_RED,
        'emergency' => TextFormat::STRONG_RED,
        'debug'     => TextFormat::GRAY
    ];

    /**
     * 最大允许的日志文件大小 (KB)
     *
     * @var integer
     */
    public $maximumSize = 1024;

    /**
     * 日志文件名称
     *
     * @var string
     */
    public $fileName = 'owoblog_run.log';

    /**
     * 记录格式
     *
     * @var string
     */
    public $logFormat = '[%s][%s][%s/%s] > %s';

    /**
     * 日志前缀
     *
     * @var string
     */
    public $logPrefix = 'OwO';


    /**
     * 写入日志到文件
     *
     * @param  string $message
     * @param  string $level
     * @param  string $color
     * @return void
     */
    public function write(string $message, string $level = 'DEBUG', string $color = TextFormat::WHITE) : void
    {
        // Check currently log file size;
        if(is_file($this->fileName) && (filesize($this->fileName) >= ($this->maximumSize * 1000))) {
            rename($this->fileName, str_replace('.log', '', $this->fileName) . date('_Y_m_d') . '.log');
        }

        // Format output message;
        $message = $color . sprintf($this->logFormat, date('Y-m-d'), date('H:i:s'), $this->logPrefix, strtoupper($level), $message) . PHP_EOL;

        if(System::isRunningWithCLI()) {
            echo TextFormat::parse($message);
        }
        if(INI::_global('owo.enableLog', true)) {
            file_put_contents(LOG_PATH . $this->fileName, TextFormat::clean($message), FILE_APPEND | LOCK_EX);
        }
    }

    public function sendEmpty() : void
    {
        echo PHP_EOL;
    }

    /**
     * 判断是否存在日志等级
     *
     * @param  string  $level
     * @return boolean
     */
    public static function hasLevel(string $level) : bool
    {
        return isset(self::LOG_LEVELS[$level]);
    }

    /**
     * 获取配色
     *
     * @param  string $level
     * @return string
     */
    public static function getColor(string $level) : string
    {
        return self::hasLevel($level) ? self::LOG_LEVELS[$level] : TextFormat::GRAY;
    }

    /**
     * 魔术方法: __call
     *
     * @param  string $level
     * @param  array  $arguments
     * @return void
     */
    public function __call(string $level, array $arguments)
    {
        $level = strtolower($level);
        if(!self::hasLevel($level)) {
            $level = 'debug';
        }
        $message = array_shift($arguments) ?? '';
        $this->write((string) $message, $level, self::getColor($level));
    }
}
?>