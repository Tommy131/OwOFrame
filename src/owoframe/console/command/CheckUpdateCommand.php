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

use owoframe\utils\TextFormat as TF;

class CheckUpdateCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$this->getLogger()->notice("Current version is: " . FRAME_VERSION . ', checking update......');
		$json = json_decode(file_get_contents('https://www.owoblog.com/checkUpdate/OwOFrame/?version=' . FRAME_VERSION));
		$this->getLogger()->sendEmpty();
		if($json->result === true) {
			$this->getLogger()->success('Currently is the newest version.');
		} else {
			if($json->msg === 'lower') {
				$message = 'Outdated version! Please go to the GitHub ' . TF::YELLOW . GITHUB_PAGE . TF::AQUA . ' or use command ' . TF::YELLOW . 'git pull'. TF::AQUA . ' to update! ';
			}
			elseif($json->msg === 'higher') {
				$message = TF::LIGHT_RED . 'Your version is too high? What have you done?';
			} else {
				$message = TF::LIGHT_RED . $json->msg;
			}
			$this->getLogger()->notice($message);
		}
		return true;
	}

	public static function getAliases() : array
	{
		return ['u', '-u'];
	}

	public static function getName() : string
	{
		return 'update';
	}

	public static function getDescription() : string
	{
		return 'Check the newest version for the OwOFrame.';
	}
}