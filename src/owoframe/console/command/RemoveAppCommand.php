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
use owoframe\helper\Helper;
use owoframe\utils\LogWriter;

class RemoveAppCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$appName = array_shift($params);
		if(empty($appName)) {
			LogWriter::info('Please enter a valid appName. Usage: ' . self::getUsage() . ' [string:appName]');
			return false;
		}
		$appName = strtolower($appName);
		if(!AppManager::hasApp($appName)) {
			LogWriter::error("Cannot find appName called '{$appName}'!");
			return false;
		}
		$answer = (string) ask('[WARNING] ARE YOU SURE THAT YOU WANT TO DELETE/REMOVE THIS APPLICATION? THIS OPERATION IS IRREVERSIBLE! [Y/N]', 'N', 'warning');
		if(strtolower($answer) === 'y') {
			LogWriter::warning('Now will remove this application forever...');
			Helper::removeDir($path = AppManager::getPath() . $appName . DIRECTORY_SEPARATOR);
			if(!is_dir($path)) {
				LogWriter::success("Removed Application '{$appName}' successfully.");
			} else {
				LogWriter::error('Somewhere was wrong that cannot remove this application!');
				return false;
			}
		}
		return true;
	}

	public static function getAliases() : array
	{
		return ['rma', '-rma'];
	}

	public static function getName() : string
	{
		return 'removeapp';
	}

	public static function getDescription() : string
	{
		return 'Look the version the OwOFrame.';
	}
}