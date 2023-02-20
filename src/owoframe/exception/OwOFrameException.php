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
 * @Date         : 2023-02-05 00:21:13
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-05 00:23:45
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
 declare(strict_types=1);
 namespace owoframe\exception;



 use Exception;

class OwOFrameException extends Exception
{
    /**
     * 获取真实文件位置
     *
     * @return string
     */
    public function getRealFile() : string
    {
        $trace = $this->getTrace();
        return $trace[1]['file'] ?? $trace[0]['file'];
    }

    /**
     * 获取真实错误行数
     *
     * @return string
     */
    public function getRealLine() : int
    {
        $trace = $this->getTrace();
        return $trace[1]['line'] ?? $trace[0]['line'];
    }

    /**
     * 获取真实错误方法
     *
     * @return string
     */
    public function getMethod() : string
    {
        $trace = $this->getTrace();
        return $trace[1]['function'] ?? $trace[0]['function'];
    }
}
?>