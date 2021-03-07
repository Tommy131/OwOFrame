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

use FilesystemIterator as FI;
use owoframe\helper\Helper;
use owoframe\utils\TextFormat as TF;

class ClearCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		if(count($params) <= 0) {
			TF::sendClear();
			echo TF::background('[SUCCESS]', 42) . '  Screen cleared.' . PHP_EOL . PHP_EOL;
		} else {
			switch(strtolower(array_shift($params))) {
				default:
					$this->execute([]);
				break;

				case 'log':
					if(($param = array_shift($params)) !== null) {
						$param = strtolower($param);
						$param = ($param === 'cli') ? $param . '_run' : (($param === 'cgi') ? '_run' : $param);
						$param = 'owoblog_' . $param . '.log';
						if(is_file(LOG_PATH . $param)) {
							unlink(LOG_PATH . $param);
							$param = TF::GREEN . "Removed log file " . TF::GOLD . "'{$param}'" . TF::GREEN . " successfully.";
						} else {
							$param = TF::LIGHT_RED . "Cannot find log file " . TF::GOLD . "'{$param}'" . TF::LIGHT_RED . "!";
						}
						Helper::logger($param);
					} else {
						$files = iterator_to_array(new FI(LOG_PATH, FI::CURRENT_AS_PATHNAME | FI::SKIP_DOTS), false);
						foreach($files as $file) {
							$baseName = basename($file);
							$ext = @end(explode('.', $baseName));
							if(strtolower($ext) === 'log') {
								unlink($file);
								Helper::logger(TF::GREEN . "Removed log file" . TF::GOLD . " '{$baseName}' " . TF::GREEN . "successfully.");
							}
						}
					}
				break;
			}
		}
		return true;
	}

	public static function getAliases() : array
	{
		return ['c', '-c'];
	}

	public static function getName() : string
	{
		return 'clear';
	}

	public static function getDescription() : string
	{
		return 'Command for clear the screen or empty a log file';
	}
}