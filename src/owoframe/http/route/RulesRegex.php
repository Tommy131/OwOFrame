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
 * @Date         : 2023-02-14 19:51:35
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-18 01:23:38
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http\route;



interface RulesRegex
{
    /**
     * 允许所有情况 (不安全)
     */
    public const ALLOW_ALL = '[allowAll]';

    /**
     * 仅匹配混合字母
     */
    public const ONLY_MIXED_LETTERS = '[onlyMixedLetters]';

    /**
     * 仅匹配大写字母
     */
    public const ONLY_UPPERCASE_LETTERS = '[onlyUppercaseLetters]';

    /**
     * 仅匹配小写字母
     */
    public const ONLY_LOWERCASE_LETTERS = '[onlyLowercaseLetters]';

    /**
     * 仅匹配混合字母与数字
     */
    public const ONLY_MIXED_LETTERS_AND_NUMBERS = '[onlyMixedLettersAndNumbers]';

    /**
     * 仅匹配普通字符串;
     */
    public const NORMAL_CHARACTER = '[normalCharacter]';

    /**
     * 仅匹配数字
     */
    public const ONLY_NUMBERS = '[onlyNumbers]';

    /**
     * 仅匹配Get格式: /?param1=1&param2=2
     */
    public const ONLY_GET_STYLE = '[onlyGetStyle]';

    /**
     * 使用默认匹配格式
     */
    public const USE_DEFAULT_STYLE = '[useDefaultStyle]';

    /**
     * 规则定义
     */
    public const ALL =
    [
        self::ALLOW_ALL                      => '/^(.*)$/imuU',
        self::ONLY_MIXED_LETTERS             => '/^[a-z]*$/imuU',
        self::ONLY_UPPERCASE_LETTERS         => '/^[A-Z]*$/muU',
        self::ONLY_LOWERCASE_LETTERS         => '/^[a-z]*$/muU',
        self::ONLY_MIXED_LETTERS_AND_NUMBERS => '/^[a-z0-9]*$/imuU',
        self::NORMAL_CHARACTER               => '/^\w+$/muU',
        self::ONLY_NUMBERS                   => '/^[0-9]*$/muU',
        self::ONLY_GET_STYLE                 => '/^(\w+=\w+)(.*)$/muU',
        self::USE_DEFAULT_STYLE              => '/^(\w+)$/muU'
    ];
}
?>