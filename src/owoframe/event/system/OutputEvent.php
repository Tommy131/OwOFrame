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
 * @Date         : 2023-02-03 23:51:38
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-14 17:51:10
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\event\system;



use owoframe\event\Event;

class OutputEvent extends Event
{
    /**
     * 输出内容
     *
     * @var string
     */
    protected $output;


    public function __construct(string $output = '')
    {
        $this->output = $output;
    }

    /**
     * 更新输出内容
     *
     * @param  string $str 输出内容
     * @return void
     */
    public function setOutput(string $str) : void
    {
        $this->__construct($str);
    }

    /**
     * 返回输出内容
     *
     * @return string
     */
    public function getOutput() : string
    {
        return $this->output;
    }

    /**
     * 输出内容
     *
     * @param string $autoClean 是否自动清空输出内容
     * @return void
     */
    public function output(bool $autoClean = false) : void
    {
        // TODO: 过滤输出内容
        echo $this->output;
        if($autoClean) {
            $this->output = '';
        }
    }
}
?>