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

class AppGeneratorCommand extends \owoframe\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$appName = array_shift($params);
		if(empty($appName)) {
			Helper::logger('Please enter a valid appName. Usage: ' . self::getUsage('newapp [string:appName]'));
			return false;
		}
		$appName = strtolower($appName);
		$upAppName = ucfirst($appName);
		$appPath = AppManager::getPath() . $appName . DIRECTORY_SEPARATOR;
		$ctlPath = $appPath . 'controller' . DIRECTORY_SEPARATOR;

		if(is_dir($appPath)) {
			Helper::logger("Application '{$appName}' may exists, please delete/rename/move it and then use this command.");
			return false;
		} else {
			mkdir($appPath, 755, true);
			mkdir($ctlPath, 755, true);

			// Make application main info class file;
			file_put_contents($appPath . $upAppName . 'App.php', str_replace(['{appName_s}', '{appName_u}'], [$appName, $upAppName], base64_decode('PD9waHAKCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioKCSBfX19fXyAgIF8gICAgICAgICAgX18gIF9fX19fICAgX19fX18gICBfICAgICAgIF9fX19fICAgX19fX18gIAoJLyAgXyAgXCB8IHwgICAgICAgIC8gLyAvICBfICBcIHwgIF8gIFwgfCB8ICAgICAvICBfICBcIC8gIF9fX3wgCgl8IHwgfCB8IHwgfCAgX18gICAvIC8gIHwgfCB8IHwgfCB8X3wgfCB8IHwgICAgIHwgfCB8IHwgfCB8ICAgICAKCXwgfCB8IHwgfCB8IC8gIHwgLyAvICAgfCB8IHwgfCB8ICBfICB7IHwgfCAgICAgfCB8IHwgfCB8IHwgIF8gIAoJfCB8X3wgfCB8IHwvICAgfC8gLyAgICB8IHxffCB8IHwgfF98IHwgfCB8X19fICB8IHxffCB8IHwgfF98IHwgCglcX19fX18vIHxfX18vfF9fXy8gICAgIFxfX19fXy8gfF9fX19fLyB8X19fX198IFxfX19fXy8gXF9fX19fLyAKCQoJKiBDb3B5cmlnaHQgKGMpIDIwMTUtMjAxOSBPd09CbG9nLURHTVQgQWxsIFJpZ2h0cyBSZXNlcmV2ZC4KCSogRGV2ZWxvcGVyOiBIYW5za2lKYXkoVGVhY2xvbikKCSogVGVsZWdyYW06IGh0dHBzOi8vdC5tZS9IYW5za2lKYXkgRS1NYWlsOiBzdXBwb3J0QG93b2Jsb2cuY29tCgkqIEdpdEh1YjogaHR0cHM6Ly9naXRodWIuY29tL1RvbW15MTMxCgkqCgkqIOWFt+S9k+S9v+eUqOaWueazleWPguiAg+eItue6p+exuzsKCSoKKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqLwoKZGVjbGFyZShzdHJpY3RfdHlwZXM9MSk7Cm5hbWVzcGFjZSBhcHBsaWNhdGlvblx7YXBwTmFtZV9zfTsKCgpjbGFzcyB7YXBwTmFtZV91fUFwcCBleHRlbmRzIFxvd29mcmFtZVxhcHBsaWNhdGlvblxBcHBCYXNlCnsKCXB1YmxpYyBmdW5jdGlvbiBpbml0aWFsaXplKCkgOiB2b2lkCgl7CgkJCgl9Cn0KPz4=')));

			// Make application default controller file;
			file_put_contents($ctlPath . $upAppName . '.php', str_replace(['{appName_s}', '{appName_u}'], [$appName, $upAppName], base64_decode('PD9waHAKCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioKCSBfX19fXyAgIF8gICAgICAgICAgX18gIF9fX19fICAgX19fX18gICBfICAgICAgIF9fX19fICAgX19fX18gIAoJLyAgXyAgXCB8IHwgICAgICAgIC8gLyAvICBfICBcIHwgIF8gIFwgfCB8ICAgICAvICBfICBcIC8gIF9fX3wgCgl8IHwgfCB8IHwgfCAgX18gICAvIC8gIHwgfCB8IHwgfCB8X3wgfCB8IHwgICAgIHwgfCB8IHwgfCB8ICAgICAKCXwgfCB8IHwgfCB8IC8gIHwgLyAvICAgfCB8IHwgfCB8ICBfICB7IHwgfCAgICAgfCB8IHwgfCB8IHwgIF8gIAoJfCB8X3wgfCB8IHwvICAgfC8gLyAgICB8IHxffCB8IHwgfF98IHwgfCB8X19fICB8IHxffCB8IHwgfF98IHwgCglcX19fX18vIHxfX18vfF9fXy8gICAgIFxfX19fXy8gfF9fX19fLyB8X19fX198IFxfX19fXy8gXF9fX19fLyAKCQoJKiBDb3B5cmlnaHQgKGMpIDIwMTUtMjAxOSBPd09CbG9nLURHTVQgQWxsIFJpZ2h0cyBSZXNlcmV2ZC4KCSogRGV2ZWxvcGVyOiBIYW5za2lKYXkoVGVhY2xvbikKCSogQ29udGFjdDogKFFRLTMzODU4MTUxNTgpIEUtTWFpbDogc3VwcG9ydEBvd29ibG9nLmNvbQoJCioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKi8KCm5hbWVzcGFjZSBhcHBsaWNhdGlvblx7YXBwTmFtZV9zfVxjb250cm9sbGVyOwoKCmNsYXNzIHthcHBOYW1lX3V9IGV4dGVuZHMgXG93b2ZyYW1lXGFwcGxpY2F0aW9uXENvbnRyb2xsZXJCYXNlCnsKCXB1YmxpYyBmdW5jdGlvbiB7YXBwTmFtZV91fSgpCgl7CgkJcmV0dXJuICdIZWxsbyB3b3JsZCEgVGhpcyBhcHBsaWNhdGlvbiB3YXMgZ2VuZXJhdGVkIGJ5IE93T0ZyYW1lOjpRdWlja0FwcEZyYW1HZW5lcmF0b3IsIHdlbGNvbWUgdG8gdXNlIGl0IDopJzsKCX0KfQo/Pg==')));

			if(AppManager::hasApp($appName)) {
				Helper::logger("Generated empty AppFrame '{$upAppName}' successfully. Please check the app path '{$appPath}' to develop it.");
				return true;
			} else {
				Helper::logger('An unknown error caused that cannot generate this empty AppFrame!');
				return false;
			}
		}
	}

	public static function getAliases() : array
	{
		return ['ag', '-n', 'new'];
	}

	public static function getName() : string
	{
		return 'newapp';
	}

	public static function getDescription() : string
	{
		return 'To generate a Application example.';
	}
}