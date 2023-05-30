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
 * @LastEditTime : 2023-02-17 22:22:24
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\event\http;



use owoframe\event\Event;
use owoframe\template\View;
use owoframe\http\Response;

class PageErrorEvent extends Event
{
    /**
     * 标题
     *
     * @var string
     */
    public $title = '404 PAGE NOT FOUND';

    /**
     * 错误响应代码
     *
     * @var integer
     */
    public $statusCode = 400;

    /**
     * 输出内容
     *
     * @var string
     */
    public $output = 'You requested page was not found.';

    /**
     * 模板渲染缓存
     *
     * @var View
     */
    public $template;


    /**
     * 创建View视图实例
     *
     * @param  string|null $fileName
     * @param  string|null $filePath
     * @return void
     */
    public function __construct(?string $fileName = null, ?string $filePath = null)
    {
        if(!$this->template instanceof View) {
            $this->template = new View($fileName ?? 'Error.html', $filePath ?? \owo\s_template_path());
        }
        $this->template->assign([
            'title'       => $this->title,
            'description' => $this->output
        ]);
    }

    /**
     * 返回视图对象
     *
     * @return View
     */
    public function template() : View
    {
        return $this->template;
    }

    /**
     * 渲染页面
     *
     * @return void
     */
    public function render() : void
    {
        $response = new Response([$this->template, 'render']);
        $response->setResponseCode($this->statusCode)->send();
    }

    /**
     * 重置输出区
     *
     * @return void
     */
    public function reset() : void
    {
        $this->template = null;
        $this->title    = null;
        $this->output   = null;
    }
}
?>