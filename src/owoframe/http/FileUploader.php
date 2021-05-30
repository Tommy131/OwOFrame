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
namespace owoframe\http;

use owoframe\helper\Helper;
use owoframe\exception\JSONException;

class FileUploader implements \owoframe\contract\Manager
{
	/* @array 默认允许上传的文件类型集合 */
	private const DEFAULT_ALLOWED_EXTS = ["mp4", "gif", "jpeg", "jpg", "png"];
	/* @array 自定义允许上传的文件类型集合 */
	private $allowedExts = [];



	/**
	 * 下方代码为文件上传方法;
	 *
	 * 错误代码说明:
	 * (int: 0) UPLOAD_ERR_OK        => 没有错误发生, 文件上传成功;
	 * (int: 1) UPLOAD_ERR_INI_SIZE  => 上传的文件超过了"php.ini"中"upload_max_filesize"选项限制的值;
	 * (int: 2) UPLOAD_ERR_FORM_SIZE => 上传文件的大小超过了HTML表单中"MAX_FILE_SIZE"选项指定的值;
	 * (int: 3) UPLOAD_ERR_PARTIAL   => 文件只有部分被上传;
	 * (int: 4) UPLOAD_ERR_NO_FILE   => 没有文件被上传;
	 *
	 * !注意! ==> 如果有必要的话, 数据返回可以使用JSON;
	 */
	public function checkUploadFile(string $uploadId, ?string $savedPath = STORAGE_PATH . 'upload/', int $maxSize = 10) : array
	{
		$fileInfo = files($uploadId);
		$fileInfo = empty($fileInfo)
		? ["name" =>"", "type" => "", "size" => "", "tmp_name" => "", "error" => UPLOAD_ERR_NO_FILE]
		: $fileInfo;
		extract($fileInfo);
		$s = (int) ini_get('post_max_size');
		if(($s * 1024000) < $size) {
			throw new JSONException(["code" => 40001, "msg" => "[40001] The server allows maximium post data size is {$s}MB, your file is too large than the limit."]);
		}

		if($size > ($maxSize * 1024000)) {
			throw new JSONException(["code" => 40002, "msg" => "[40002] File size '{$name}' is too large than the server allowed (max. {$maxSize} MB)!"]);
		}
		if($this->canUpload(@end(explode(".", $name)))) {
			if(($savedPath !== null) && !is_dir($savedPath)) {
				mkdir($savedPath, 755, true);
			}
			move_uploaded_file($tmp_name, $savedPath . $name);
		} else  {
			throw new JSONException(["code" => 40003, "msg" => "[40003] File cannot be upload because the server denied the extension!"]);
		}
		return $fileInfo;
	}

	/**
	 * @method      addAllowedExt
	 * @description 添加一个文件类型到允许上传的文件类型列表;
	 * @param       string[ext|文件类型]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function addAllowedExt(string $ext) : void
	{
		if(!$this->canUpload($ext)) {
			$this->allowedExts[] = $ext;
		}
	}

	/**
	 * @method      delAllowedExt
	 * @description 添加一个文件类型到允许上传的文件类型列表;
	 * @param       string[ext|文件类型]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function delAllowedExt(string $ext) : void
	{
		$ext = strtolower($ext);
		if($this->canUpload($ext) && (($key = array_search($ext, $this->allowedExts)) !== false)) {
			unset($this->allowedExts[$key]);
		}
	}

	/**
	 * @method      canUpload
	 * @description 检测文件类型是否允许上传到服务器;
	 * @param       string[ext|文件类型]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function canUpload(string $ext) : bool
	{
		return isset(Helper::MIMETYPE[$ext]) && in_array($ext, $this->getAllowedExts());
	}

	/**
	 * @method      getAllowedExts
	 * @description 检测文件类型是否允许上传到服务器;
	 * @param       string[ext|文件类型]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-09-10 18:49
	*/
	public function getAllowedExts() : array
	{
		return array_merge(self::DEFAULT_ALLOWED_EXTS, $this->allowedExts);
	}
}