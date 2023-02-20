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
 * @Date         : 2023-02-09 19:27:44
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-14 17:26:12
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



use owoframe\utils\MIMEType;
use owoframe\exception\JSONException;

class FileUploader
{
    /**
     * 允许上传的文件类型集合
     *
     * @access private
     * @var array
     */
    private $allowedExts = [];


    /**
     * 下方代码为文件上传方法
     *
     * 错误代码说明:
     * (int: 0) UPLOAD_ERR_OK        => 没有错误发生, 文件上传成功;
     * (int: 1) UPLOAD_ERR_INI_SIZE  => 上传的文件超过了'php.ini'中'upload_max_filesize'选项限制的值;
     * (int: 2) UPLOAD_ERR_FORM_SIZE => 上传文件的大小超过了HTML表单中'MAX_FILE_SIZE'选项指定的值;
     * (int: 3) UPLOAD_ERR_PARTIAL   => 文件只有部分被上传;
     * (int: 4) UPLOAD_ERR_NO_FILE   => 没有文件被上传;
     *
     * !注意! ==> 如果有必要的话, 数据返回可以使用JSON;
     *
     * @param  string  $uploadId 上传ID
     * @param  string  $savePath 保存到文件路径
     * @param  int     $maxSize  允许的最大上传大小
     * @return array
     */
    public function checkUploadFile(string $uploadId, ?string $savePath = null, int $maxSize = 10) : array
    {
        if(!$savePath) {
            $savePath = \owo\storage_path('upload', true);
        }
        $fileInfo = \owo\files($uploadId);
        $fileInfo = empty($fileInfo)
        ? ['name' =>'', 'type' => '', 'size' => '', 'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE]
        : $fileInfo;
        extract($fileInfo);
        $s = (int) ini_get('post_max_size');
        if(($s * 1024000) < $size) {
            throw new JSONException(['code' => 40001, 'msg' => "[40001] The server allows maximum post data size is {$s}MB, your file is too large than the limit."]);
        }

        if($size > ($maxSize * 1024000)) {
            throw new JSONException(['code' => 40002, 'msg' => "[40002] File size '{$name}' is too large than the server allowed (max. {$maxSize} MB)!"]);
        }
        if($this->canUpload(@end(explode('.', $name)))) {
            if(($savePath !== null) && !is_dir($savePath)) {
                mkdir($savePath, 755, true);
            }
            move_uploaded_file($fileInfo['tmp_name'], $savePath . $name);
        } else {
            throw new JSONException(['code' => 40003, 'msg' => '[40003] File cannot be upload because the server denied the extension!']);
        }
        return $fileInfo;
    }

    /**
     * 添加一个文件类型到允许上传的文件类型列表
     *
     * @param  string $ext
     * @return void
     */
    public function addAllowedExt(string $ext) : void
    {
        if(!$this->canUpload($ext)) {
            $this->allowedExts[] = $ext;
        }
    }

    /**
     * 添加一个文件类型到允许上传的文件类型列表
     *
     * @param  string $ext
     * @return void
     */
    public function delAllowedExt(string $ext) : void
    {
        $ext = strtolower($ext);
        $key = array_search($ext, $this->allowedExts);
        if($this->canUpload($ext) && ($key !== false)) {
            unset($this->allowedExts[$key]);
        }
    }

    /**
     * 检测文件类型是否允许上传到服务器
     *
     * @param  string $ext
     * @return boolean
     */
    public function canUpload(string $ext) : bool
    {
        return isset(MIMEType::ALL[$ext]) && in_array($ext, $this->getAllowedExts());
    }

    /**
     * 检测文件类型是否允许上传到服务器
     *
     * @param  string $ext
     * @return array
     */
    public function getAllowedExts() : array
    {
        return $this->allowedExts;
    }
}
?>