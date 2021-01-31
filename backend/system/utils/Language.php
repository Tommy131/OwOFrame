<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

namespace backend\system\utils;

use backend\system\exception\ResourceMissedException;

class Language
{
	/* @string 语言包名 */
	private $package;
	/* @string 文件路径 */
	private $file;
	/* @string 语言标签 */
	private $lang;


	public function __construct(string $package, string $file, string $lang = 'en-US')
	{
		if(!file_exists($file)) {
			throw new ResourceMissedException('LanguagePack', $file);
		}
		$this->package = $package;
		$this->file    = $file;
		$this->lang    = $lang;
	}
}