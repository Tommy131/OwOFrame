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
 * @Date         : 2023-02-15 19:36:28
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-15 20:10:46
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\utils;



use Throwable;

use owoframe\utils\TextColorOutput as TCO;

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
        'debug'     => TCO::GRAY,
        'success'   => TCO::GREEN,
        'info'      => TCO::WHITE,
        'notice'    => TCO::AQUA,
        'warning'   => TCO::GOLD,
        'error'     => TCO::RED,
        'alert'     => TCO::LIGHT_RED,
        'emergency' => TCO::STRONG_RED
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
    public function write(string $message, string $level = 'DEBUG', string $color = TCO::WHITE) : void
    {
        // Check currently log file size
        if(is_file($this->fileName) && (filesize($this->fileName) >= ($this->maximumSize * 1000))) {
            rename($this->fileName, str_replace('.log', '', $this->fileName) . date('_Y_m_d') . '.log');
        }

        // Format output message
        $message = $color . sprintf($this->logFormat, date('Y-m-d'), date('H:i:s'), $this->logPrefix, strtoupper($level), $message) . PHP_EOL;

        if(\owo\php_is_cli()) {
            echo TCO::parse($message);
        }

        if(\owo\_global('owo.enableLog', true)) {
            $this->writeToFile(TCO::clean($message));
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
        $mainFile = $this->fileName;
        $start    = self::COPY_LINE_START;
        $end      = self::COPY_LINE_END;
        $file     = file_get_contents($mainFile);
        $file     = $file ? $file : '';

        if(!preg_match("/{$start}(.*){$end}/s", $file, $lines)) {
            return;
        }
        $lines = $lines[0];

        $lines = explode("\n", $lines);
        $count = count($lines);
        unset($lines[0], $lines[$count - 1]);
        $lines = implode("\n", $lines);

        // 替换删除行
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
        return self::hasLevel($level) ? self::LOG_LEVELS[$level] : TCO::GRAY;
    }

    /**
     * 写入日志到文件
     *
     * @return void
     */
    private function writeToFile(string $message, ?string $filePath = null) : void
    {
        file_put_contents($filePath ?? \owo\log_path($this->fileName), $message, FILE_APPEND | LOCK_EX);
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

        if($message instanceof Throwable) {
            $message = $message->__toString();
        }
        elseif(!is_string($message)) {
            $message = (string) $message;
        }

        $this->write($message, $level, self::getColor($level));
    }
}
?>