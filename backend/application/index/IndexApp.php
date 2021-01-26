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
	*
	* 具体使用方法参考父级类;
	*
************************************************************************/

declare(strict_types=1);
namespace backend\application\index;


class IndexApp extends \backend\system\app\AppBase
{
	public function initialize() : void
	{
		// $this->setDefaultController("Index");
	}
	
	public static function autoTo404Page() : bool
	{
		return true;
	}
}
?>