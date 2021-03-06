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
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com

************************************************************************/

declare(strict_types=1);
namespace owoframe\console\command;

use owoframe\helper\Helper;
use owoframe\utils\TextFormat;

class ClearCommand extends \owoframe\console\CommandBase
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
		return 'clear the cmd/shell\'s screen.';
	}
}