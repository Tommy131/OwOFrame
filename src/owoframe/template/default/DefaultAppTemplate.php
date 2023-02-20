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
 * @Date         : 2023-02-20 05:32:54
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-20 05:32:54
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace application\applicationName;



use owoframe\application\Application;

class className extends Application
{
    /**
     * 应用程序配置文件
     *
     * @var array
     */
    protected static $config =
    [
        # 应用程序名称ID
        'name'        => 'applicationName',
        # 基本信息
        'author'      => 'OwOTeam',
        'version'     => '1.0.0',
        'description' => 'default description',
        # 允许应用程序在PHP模式下加载 (CGI, CLI)
        'loadMode'    => ['cgi', 'cli']
    ];


	public function initialize() : void
	{
	}
}
?>