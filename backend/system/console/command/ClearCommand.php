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

namespace backend\system\console\command;

use backend\system\utils\TextFormat;

class ClearCommand extends \backend\system\console\CommandBase
{
	public function execute(array $params) : bool
	{
		TextFormat::sendClear();
		echo TextFormat::background('[SUCCESS]', 42) . '  Screen cleared.' . PHP_EOL . PHP_EOL;
		return true;
	}

	public static function getAliases() : array
	{
		return [];
	}

	public static function getName() : string
	{
		return 'clear';
	}

	public static function getDescription() : string
	{
		return 'Look the version the OwOFrame.';
	}
}