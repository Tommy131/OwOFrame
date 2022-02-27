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
namespace owoframe\console\command;

use owoframe\application\AppManager;
use owoframe\utils\Logger;

class AppCheckCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$appName = array_shift($params);
		if(empty($appName)) {
			Logger::info('Please enter a valid appName. Usage: ' . self::getUsage() . ' [string:appName]');
			return false;
		}
		$appName = strtolower($appName);
		$appPath = AppManager::getPath() . $appName . DIRECTORY_SEPARATOR;
		$class   = '\\application\\' . $appName . '\\' . ucfirst($appName) . 'App';

		if(is_dir($appPath) && class_exists($class)) {
			Logger::success("Application '{$appName}' is exists.");
			Logger::info("----------[INFO-LIST]'{$appName}'----------");
			Logger::info('Author: ' . $class::getAuthor());
			Logger::info('Description: ' . $class::getDescription());
			Logger::info('Version: ' . $class::getVersion());
			return true;
		}
		Logger::info("Application '{$appName}' does not exists.");
		return false;
	}

	public static function getAliases() : array
	{
		return ['ac', '-ac'];
	}

	public static function getName() : string
	{
		return 'appcheck';
	}

	public static function getDescription() : string
	{
		return 'Check if an app exists.';
	}
}