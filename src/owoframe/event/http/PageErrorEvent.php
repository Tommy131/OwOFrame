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
namespace owoframe\event\http;

use owoframe\application\View;
use owoframe\event\Event;
use owoframe\http\Response;

class PageErrorEvent extends Event
{

    /**
     * 标题
     *
     * @var string
     */
    public static $title = '404 PAGE NOT FOUND';

    /**
     * 错误响应代码
     *
     * @var integer
     */
    public static $statusCode = 400;

    /**
     * 输出内容
     *
     * @var string
     */
    public static $output = 'You requested page was not found.';

    /**
     * 模板渲染缓存
     *
     * @var View
     */
    public static $view;

    /**
     * 创建View视图实例
     *
     * @param  string  $fileName
     * @param  string  $filePath
     * @return View
     */
    public static function create(string $fileName = 'Error.html', string $filePath = FRAMEWORK_PATH . 'template') : View
    {
        if(!static::$view instanceof View) {
            static::$view = new View($fileName, $filePath);
        }
        return static::$view;
    }

    /**
     * 渲染页面
     *
     * @return void
     */
    public static function render() : void
    {
        static::$view->assign([
            'title'       => static::$title,
            'description' => static::$output
        ]);
        $response = new Response([static::$view, 'render']);
        $response->setResponseCode(static::$statusCode)->sendResponse();
    }
}