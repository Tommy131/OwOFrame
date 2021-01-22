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

declare(strict_types=1);
namespace backend\system\exception;

class ParameterErrorException extends OwOFrameException
{
	public function __construct(string $param, string $needType, string $class, int $code = 0, \Throwable $previous = null)
	{
		$currType = gettype($param);
		parent::__construct("Parameter '{$param}' should be '{$needType}', '{$currType}' is given. Caused by App {$class}::Class", $code, $previous);
	}
}