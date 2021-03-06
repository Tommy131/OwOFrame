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
	* GitHub: https://github.com/Tommy131

************************************************************************/

declare(strict_types=1);
namespace owoframe\console\command;

use owoframe\MasterManager;
use owoframe\helper\Helper;
use owoframe\utils\TextFormat as TF;

class HelpCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$console  = MasterManager::getInstance()->getManager('console');
		$commands = $console->getCommands();
		ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
		if(count($params) <= 0) {
			Helper::logger(TF::GOLD . "---------- Registered Commands: " . TF::GREEN . count($commands) . TF::GOLD . " ----------");
			foreach($commands as $command => $class) {
				Helper::logger(TF::GREEN . "{$command}: " . TF::WHITE . $class->getDescription());
			}
			Helper::logger(TF::WHITE . "Use '" . self::getUsage() . "'" . TF::WHITE . " to look details.");
		} else {
			$command = strtolower(array_shift($params));
			if(!isset($commands[$command])) {
				Helper::logger(TF::RED . "Command " . TF::GOLD . $command . TF::RED . " does not exists!");
			} else {
				$command = $commands[$command];
				Helper::logger(TF::WHITE . "---[" . TF::NORMAL_YELLOW . "Details" . TF::STRONG_RED . "@" . TF::GREEN . $command->getName() . TF::WHITE . "]---");
				Helper::logger(TF::BLUE . "CommandName: " . TF::WHITE . $command->getName());
				Helper::logger(TF::BLUE . "AliasName:   " . TF::WHITE . implode(', ', $command->getAliases()));
				Helper::logger(TF::BLUE . "Usage:       " . TF::WHITE . $command->getUsage());
				Helper::logger(TF::BLUE . "Description: " . TF::WHITE . $command->getDescription());
			}
		}
		return true;
	}

	public static function getAliases() : array
	{
		return ['h', '-h', '-help', '--h', '--help'];
	}

	public static function getName() : string
	{
		return 'help';
	}

	public static function getDescription() : string
	{
		return 'A Helper command for OwOFrame CLI Command.';
	}

	public static function getUsage() : string
	{
		return TF::LIGHT_RED . "php owo help " . TF::GOLD . "[" . TF::AQUA . "commandName" . TF::GOLD . "]";
	}
}