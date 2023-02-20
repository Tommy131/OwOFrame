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
 * @Date         : 2023-02-09 19:00:55
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 19:11:57
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\application\standard\controller;



use owoframe\application\Controller;
use owoframe\http\FileUploader;
use owoframe\template\View;

class Upload extends Controller
{
    /**
     * 上传ID
     *
     * @var string
     */
    private static $uploadId = 'upload';


    /**
     * 新建视图
     *
     * @return void
     */
    public function Upload()
    {
        $view = new View();
        $view->assign([
            'uploadUrl' => '/index/upload/handler',
            'uploadId'  => self::$uploadId
        ]);
        return $view->render();
    }

    /**
     * 处理文件上传的方法
     *
     * @return void
     */
    public function handler()
    {
        $handler = new FileUploader;
        $handler->addAllowedExt('docx');
        $handler->addAllowedExt('pdf');
        $handler = $handler->checkUploadFile(self::$uploadId);
        return ($handler['error'] === 0) ? "OK" : "NONE";
    }
}
?>