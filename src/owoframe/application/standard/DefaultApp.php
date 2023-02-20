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
 * @Date         : 2023-02-09 19:01:28
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-17 23:44:25
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\application\standard;

use owoframe\application\Application;

class DefaultApp extends Application
{
    /**
     * 应用程序配置文件
     *
     * @var array
     */
    protected static $config =
    [
        # 基本信息
        'author'            => 'OwOTeam',
        'version'           => '1.0.0',
        'description'       => 'Default Application',
        # 允许应用程序在PHP模式下加载 (CGI, CLI)
        'loadMode'          => ['cgi', 'cli']
    ];

    /**
     * 默认控制器
     *
     * @var string|null
     */

    protected $defaultController = 'Index';


    /**
     * 初始化方法
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setDefaultController('Index');
    }

    /**
     * 应用程序名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'default';
    }
}
?>