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

use Throwable;

use owoframe\System;
use owoframe\utils\TextFormat;
use owoframe\exception\OwOLogException;

class Logger
{
    /**
     * 复制区域开始识别标签
     */
    public const COPY_LINE_START = '--- COPY LINE BEGIN hntQrT1QfAvCe8RFFmcP ---';

    /**
     * 复制区域结束识别标签
     */
    public const COPY_LINE_END = '--- COPY LINE END hntQrT1QfAvCe8RFFmcP ---';

    /**
     * 日志等级 (对应输出的颜色)
     */
    public const LOG_LEVELS =
    [
        'debug'     => TextFormat::GRAY,
        'success'   => TextFormat::GREEN,
        'info'      => TextFormat::WHITE,
        'notice'    => TextFormat::AQUA,
        'warning'   => TextFormat::GOLD,
        'error'     => TextFormat::RED,
        'alert'     => TextFormat::LIGHT_RED,
        'emergency' => TextFormat::STRONG_RED
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
        if(_global('owo.enableLog', true)) {
            $this->writeToFile(TextFormat::clean($message));
        }
    }

    /**
     * 插入复制区域标签
     *
     * @param  boolean $isStart
     * @return void
     */
    public function insertCopyLine(bool $isStart = true) : void
    {
        $this->writeToFile(($isStart ? self::COPY_LINE_START : self::COPY_LINE_END) . PHP_EOL);
    }

    /**
     * 将复制区域的日志记录粘贴到新的文件内
     *
     * @param  string  $filePath
     * @param  boolean $deleteArea
     * @return void
     */
    public function copyAreaToNewFile(string $filePath, bool $deleteArea = false) : void
    {
        $mainFile = LOG_PATH . $this->fileName;
        if(!file_exists($mainFile)) {
            throw new OwOLogException("Log file {$mainFile} not found!");
        }

        $start = self::COPY_LINE_START;
        $end   = self::COPY_LINE_END;

        if(!preg_match("/{$start}(.*){$end}/s", $file = file_get_contents($mainFile), $lines)) {
            return;
        }
        $lines = $lines[0];

        $lines = explode("\n", $lines);
        $count = count($lines);
        unset($lines[0], $lines[$count - 1]);
        $lines = implode("\n", $lines);

        // 替换删除行;
        if($deleteArea) {
            $file = str_replace($lines, "The following {$count} lines has been moved to the new log file {$filePath}.", $file);
        }
        $file = str_replace([ PHP_EOL . $start, $end . PHP_EOL], '', $file);
        file_put_contents($mainFile, $file);

        $this->writeToFile($lines, $filePath);
    }

    /**
     * 发送空行
     *
     * @return void
     */
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
     * 写入日志到文件
     *
     * @return void
     */
    private function writeToFile(string $message, ?string $filePath = null) : void
    {
        file_put_contents($filePath ?? LOG_PATH . $this->fileName, $message, FILE_APPEND | LOCK_EX);
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

        $throwMessage = array_shift($arguments) ?? false;
        if($throwMessage) {
            $throwable = array_shift($arguments) ?? null;
            if(!$throwable instanceof Throwable) {
                $throwable = new OwOLogException($message);
            }
            throw $throwable;
        }
    }
}
?>