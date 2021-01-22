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

class InvalidAppException extends OwOFrameException
{
	public function __construct(string $appName, string $reason, string $class, int $code = 0, \Throwable $previous = null)
	{
		parent::__construct("[AppExceptionHandler] Matched app '{$appName}' is failed to load, because: {$reason}! Caused by {$class}::Class", $code, $previous);
	}
}