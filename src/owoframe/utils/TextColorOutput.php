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
 * @Date         : 2023-02-02 16:41:33
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-02 17:26:44
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\utils;



class TextColorOutput
{

    /**
     * 定义§的编码
     */
    public const PREFIX = "\xc2\xa7";

    /* 定义标准颜色 | Define Standard Colors */
    /**
     * 黑色
     */
    public const BLACK = self::PREFIX . '0';

    /**
     * 白色
     */
    public const WHITE = self::PREFIX . 'w';

    /**
     * 灰色
     */
    public const GRAY = self::PREFIX . 'g';

    /**
     * 红色
     */
    public const RED        = self::PREFIX . '1';
    public const LIGHT_RED  = self::PREFIX . 'L';
    public const STRONG_RED = self::PREFIX . 'S';

    /**
     * 橙色
     */
    public const ORANGE = self::PREFIX . '2';

    /**
     * 黄色
     */
    public const YELLOW        = self::PREFIX . '3';
    public const NORMAL_YELLOW = self::PREFIX . '4';

    /**
     * 绿色
     */
    public const GREEN = self::PREFIX . '5';

    /**
     * 水色 (亮蓝色)
     */
    public const AQUA = self::PREFIX . '6';

    /**
     * 蓝色 (标准色)
     */
    public const BLUE = self::PREFIX . '7';

    /**
     * 金色
     */
    public const GOLD = self::PREFIX . '8';

    /**
     * 紫色
     */
    public const PURPLE = self::PREFIX . '9';
    public const LILA   = self::PREFIX . 'l';


    /**
     * 加粗
     */
    public const BOLD = self::PREFIX . 'b';

    /**
     * 斜体字
     */
    public const ITALIC = self::PREFIX . 'i';

    /**
     * 重置颜色
     */
    public const RESET = self::PREFIX . 'r';

    /**
     * 删除线
     */
    public const DELETE_LINE = self::PREFIX . 's';

    /**
     * 删除线(正规叫法)
     */
    public const STRIKETHROUGH = self::DELETE_LINE;

    /**
     * 下划线
     */
    public const UNDERLINE = self::PREFIX . 'u';

    /**
     * 分割字符串
     *
     * @param  string $str 传入的字符串
     * @return array
     */
    public static function split(string $str) : array
    {
        return preg_split('/(' . self::PREFIX . '[0-9a-z])/iU', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * 转换颜色字符
     *
     * @param  string $str 传入的字符串
     * @return string
     */
    public static function clean(string $str) : string
    {
        return str_replace(self::PREFIX, '', preg_replace('/' . self::PREFIX . '[0-9a-z]/iU', '', $str));
    }

    /**
     * 解析传入普通字符转换成颜色字符
     *
     * @param  string $input 传入的字符串
     * @return string
     */
    public static function parse(string $input) : string
    {
        $form   = "\033[38;5;%sm";
        $output = '';
        foreach(self::split($input) as $v) {
            switch($v)
            {
                // When did not match
                default:
                    $output .= $v;
                break;

                // Match Colors
                case self::BLACK:
                    $output .= sprintf($form, '16');
                break;

                case self::WHITE:
                    // $output .= sprintf($form, '7');
                    $output .= sprintf($form, '15');
                break;

                case self::GRAY:
                    $output .= sprintf($form, '8');
                break;


                case self::RED:
                    $output .= sprintf($form, '1');
                break;
                case self::LIGHT_RED:
                    $output .= sprintf($form, '9');
                break;
                case self::STRONG_RED:
                    $output .= sprintf($form, '196');
                break;

                case self::ORANGE:
                    $output .= sprintf($form, '3');
                break;

                case self::YELLOW:
                    $output .= sprintf($form, '226');
                break;
                case self::NORMAL_YELLOW:
                    $output .= sprintf($form, '190');
                break;

                case self::GREEN:
                    $output .= sprintf($form, '46');
                break;

                case self::AQUA:
                    $output .= sprintf($form, '51');
                break;

                case self::BLUE:
                    $output .= sprintf($form, '12');
                break;

                case self::GOLD:
                    $output .= sprintf($form, '226');
                break;

                case self::PURPLE:
                    $output .= sprintf($form, '5');
                break;
                case self::LILA:
                    $output .= sprintf($form, '13');
                break;

                // Match Symbols
                case self::BOLD:
                    $output .= "\033[1m";
                break;

                case self::ITALIC:
                    $output .= "\033[3m";
                break;

                case self::RESET:
                    $output .= "\033[0m";
                break;

                case self::DELETE_LINE:
                case self::STRIKETHROUGH:
                    $output .= "\033[9m";
                break;

                case self::UNDERLINE:
                    $output .= "\033[4m";
                break;

            }
        }
        return $output . "\033[0m";
    }

    /**
     * 自定义取色器
     *
     * @param  int    $num 颜色编号
     * @param  string $str 传入的字符
     * @return string
     */
    public static function color(string $str, int $num = 15) : string
    {
        // Maximal color range cannot bigger than 250
        if($num > 250) $num = 15;
        return "\033[38;5;{$num}m{$str}\033[0m";
    }

    /**
     * 背景取色器
     *
     * @param  string $str 传入的字符
     * @param  int    $num 背景颜色编号
     * @return string
     */
    public static function background(string $str, int $num = 40) : string
    {
        // 40: 黑底白字  41: 红底白字 42: 绿底白字  43: 黄底白字   44: 蓝底白字  45: 紫底白字   46: 天蓝底白字 47: 灰底白字
        // 40: Black background 41: Red background 42: Green background 43: Yellow background 44: Blue background 45: Purple background 46: GrassGreen background  47: Gray background
        // Color number should be in the range 40 ~ 47
        if(($num > 47) || ($num < 40)) $num = 40;
        return "\033[{$num}m{$str}\033[0m";
    }

    /**
     * 发送一个清屏代码
     *
     * @return void
     */
    public static function sendClear() : void
    {
        echo "\033[2J\033[0m" . PHP_EOL;
    }

    /**
     * 输出颜色测试文本
     *
     * @param  string $str
     * @return void
     */
    public static function sendTestColorText(string $str = 'Hello World!') : void
    {
        for($i = 0; $i < 256; $i++) {
            echo "Current Color-No. {$i}: \033[38;5;{$i}m{$str}\033[0m" . PHP_EOL;
        }
    }

    /**
     * 输出带背景颜色的测试文本
     *
     * @param  string $str
     * @return void
     */
    public static function sendTestColorTextWithBackground(string $str = 'Hello World!') : void
    {
        for($i = 40; $i < 48; $i++) {
            echo "Current Color-No. {$i}: \033[38;5;{$i}m{$str}\033[0m" . PHP_EOL;
        }
    }

    /**
     * 在CLI中输出进度条
     *
     * @param  integer       $start
     * @param  integer       $count
     * @param  string        $customString
     * @param  callable|null $callback
     *
     * ~ 注意此处的callback返回值必须以数组方式返回元素 'status' 和 'message', 否则无法输出callback信息!
     *
     * ~ Note that the callback return value here must return the elements 'status' and 'message' as an array,
     * ~ otherwise the callback information cannot be output!
     * @return void
     */
    public static function sendProgressBar(int $start = 0, int $count = 100, string $customString = '', ?callable $callback = null) : void
    {
        $process = '';
        for($i = 1; $i <= $start; $i++) {
            $process .= '▊'; // 3 bytes
        }

        $lengthString = strlen($customString);
        if($lengthString > 0) {
            $maximumAllowedStringPercent = 60 / 105;
            if($lengthString / 105 > $maximumAllowedStringPercent) {
                $customString = substr($customString, 0, 50) . '...';
                $lengthString = strlen($customString);
            }
            echo "\x1b]0;OwOFrame CLI (ver." . (OWO_VERSION ?? '1.0.0-dev') . ') - ' . $customString . "\x07";
            $process = substr($process, 0, (int) ((strlen($process) - 9) * (1 - $lengthString / 105)));
        }

        $percent = round($start / $count * 100);
        if($percent <= 100) {
            echo "\033[?25l";
            echo "\033[105D";
            echo "\033[" . ($count) . 'E';

            $result = call_user_func($callback);
            if(isset($result['status'], $result['message'])) {
                echo "\033[2F" . self::background($result['status'] ? 'SUCCESS' : 'Error', 42) . self::color(' Output: ' . $result['message'], $result['status'] ? 46 : 196);
            }
            echo "\033[2E";
            echo "\033[32m{$process}\033[33m {$percent}%  " . $customString . "\033[0m";
        }

        if($percent == 100) {
            echo "\n\33[?25h\033[0m\n";
        }
    }
}
?>