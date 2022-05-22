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
namespace application\index\controller;

use owoframe\application\AppBase;
use owoframe\http\FileUploader;

class Upload extends \owoframe\application\ViewBase
{
	private static $uploadId = 'upload';

	public function __construct(AppBase $app)
	{
		self::showUsedTimeDiv(false);
		parent::__construct($app);
	}

	public function Upload()
	{
		$this->assign([
			'uploadUrl' => '/index/upload/handler',
			'uploadId'  => self::$uploadId
		]);
		return $this->render();
	}

	// TODO: 处理文件上传的方法;
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