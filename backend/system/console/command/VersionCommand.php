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

namespace backend\system\console\command;

class VersionCommand extends \backend\system\console\CommandBase
{
	public function execute(array $params) : bool
	{
		\OwOBootStrap\logger("Welcome to use OwOFrame :) Current version is: " . APP_VERSION);
		return true;
	}

	public static function getAliases() : array
	{
		return ['v', '-v', '-ver'];
	}

	public static function getName() : string
	{
		return 'version';
	}

	public static function getDescription() : string
	{
		return 'Look the version the OwOFrame.';
	}
}