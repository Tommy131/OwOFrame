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
 * @Date         : 2023-02-02 18:49:33
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-20 19:35:19
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe;



class Priority
{
    /**
     * 绝对优先级别 (仅允许一个实例)
     */
    public const ABS_HIGHEST = 0;

    /**
     * 最高优先级别
     */
    public const HIGHEST = 1;

    /**
     * 中等优先级别
     */
    public const MEDIUM = 2;

    /**
     * 普通优先级别
     */
    public const NORMAL = 3;

    /**
     * 最低优先级别
     */
    public const LOWEST = 4;

    /**
     * 所有优先级别列表
     */
    public const ALL =
    [
        self::ABS_HIGHEST => 'absolute_highest',
        self::HIGHEST     => 'highest',
        self::MEDIUM      => 'medium',
        self::NORMAL      => 'normal',
        self::LOWEST      => 'lowest'
    ];

    /**
     * 通过字符串返回整型
     *
     * @param  string       $str
     * @return integer|null
     */
    public static function getFromString(string $str) : ?int
    {
        return array_flip(self::ALL)[$str] ?? null;
    }

    /**
     * 判断是否存在某个优先级别
     *
     * @param  integer $priority
     * @return boolean
     */
    public static function has(int $priority) : bool
    {
        return isset(self::ALL[$priority]);
    }
}
?>