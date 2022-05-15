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

use owoframe\socket\WebSocket;

class WebSocketCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		if(count($params) > 0) {
			$ip   = array_shift($params);
			$port = (count($params) > 0) ? array_shift($params) : null;
		}
		$ws = new WebSocket($ip ?? '0.0.0.0', $port ?? 32710);
		$ws->run();
		return true;
	}

	public static function getAliases() : array
	{
		return [];
	}

	public static function getName() : string
	{
		return 'ws';
	}

	public static function getDescription() : string
	{
		return 'Command for WebSocket';
	}
}