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
			file_put_contents($appPath . $upAppName . 'App.php', str_replace(['{appName_s}', '{appName_u}'], [$appName, $upAppName], base64_decode('PD9waHAKCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioKCSBfX19fXyAgIF8gICAgICAgICAgX18gIF9fX19fICAgX19fX18gICBfICAgICAgIF9fX19fICAgX19fX18KCS8gIF8gIFwgfCB8ICAgICAgICAvIC8gLyAgXyAgXCB8ICBfICBcIHwgfCAgICAgLyAgXyAgXCAvICBfX198Cgl8IHwgfCB8IHwgfCAgX18gICAvIC8gIHwgfCB8IHwgfCB8X3wgfCB8IHwgICAgIHwgfCB8IHwgfCB8Cgl8IHwgfCB8IHwgfCAvICB8IC8gLyAgIHwgfCB8IHwgfCAgXyAgeyB8IHwgICAgIHwgfCB8IHwgfCB8ICBfCgl8IHxffCB8IHwgfC8gICB8LyAvICAgIHwgfF98IHwgfCB8X3wgfCB8IHxfX18gIHwgfF98IHwgfCB8X3wgfAoJXF9fX19fLyB8X19fL3xfX18vICAgICBcX19fX18vIHxfX19fXy8gfF9fX19ffCBcX19fX18vIFxfX19fXy8KCgkqIENvcHlyaWdodCAoYykgMjAxNS0yMDIxIE93T0Jsb2ctREdNVC4KCSogRGV2ZWxvcGVyOiBIYW5za2lKYXkoVG9tbXkxMzEpCgkqIFRlbGVncmFtOiAgaHR0cHM6Ly90Lm1lL0hhbnNraUpheQoJKiBFLU1haWw6ICAgIHN1cHBvcnRAb3dvYmxvZy5jb20KCSogR2l0SHViOiAgICBodHRwczovL2dpdGh1Yi5jb20vVG9tbXkxMzEKCioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiovCgpkZWNsYXJlKHN0cmljdF90eXBlcz0xKTsKbmFtZXNwYWNlIGFwcGxpY2F0aW9uXHthcHBOYW1lX3N9OwoKCmNsYXNzIHthcHBOYW1lX3V9QXBwIGV4dGVuZHMgXG93b2ZyYW1lXGFwcGxpY2F0aW9uXEFwcEJhc2UKewoJcHVibGljIGZ1bmN0aW9uIGluaXRpYWxpemUoKSA6IHZvaWQKCXsKCX0KCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGdldE5hbWUoKSA6IHN0cmluZwoJewoJCXJldHVybiAne2FwcE5hbWVfc30nOwoJfQoKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gYXV0b1RvNDA0UGFnZSgpIDogYm9vbAoJewoJCXJldHVybiB0cnVlOwoJfQp9Cj8+')));

			// Make application default controller file;
			file_put_contents($ctlPath . $upAppName . '.php', str_replace(['{appName_s}', '{appName_u}'], [$appName, $upAppName], base64_decode('PD9waHAKCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioKCSBfX19fXyAgIF8gICAgICAgICAgX18gIF9fX19fICAgX19fX18gICBfICAgICAgIF9fX19fICAgX19fX18KCS8gIF8gIFwgfCB8ICAgICAgICAvIC8gLyAgXyAgXCB8ICBfICBcIHwgfCAgICAgLyAgXyAgXCAvICBfX198Cgl8IHwgfCB8IHwgfCAgX18gICAvIC8gIHwgfCB8IHwgfCB8X3wgfCB8IHwgICAgIHwgfCB8IHwgfCB8Cgl8IHwgfCB8IHwgfCAvICB8IC8gLyAgIHwgfCB8IHwgfCAgXyAgeyB8IHwgICAgIHwgfCB8IHwgfCB8ICBfCgl8IHxffCB8IHwgfC8gICB8LyAvICAgIHwgfF98IHwgfCB8X3wgfCB8IHxfX18gIHwgfF98IHwgfCB8X3wgfAoJXF9fX19fLyB8X19fL3xfX18vICAgICBcX19fX18vIHxfX19fXy8gfF9fX19ffCBcX19fX18vIFxfX19fXy8KCgkqIENvcHlyaWdodCAoYykgMjAxNS0yMDIxIE93T0Jsb2ctREdNVC4KCSogRGV2ZWxvcGVyOiBIYW5za2lKYXkoVG9tbXkxMzEpCgkqIFRlbGVncmFtOiAgaHR0cHM6Ly90Lm1lL0hhbnNraUpheQoJKiBFLU1haWw6ICAgIHN1cHBvcnRAb3dvYmxvZy5jb20KCSogR2l0SHViOiAgICBodHRwczovL2dpdGh1Yi5jb20vVG9tbXkxMzEKCioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiovCgpuYW1lc3BhY2UgYXBwbGljYXRpb25ce2FwcE5hbWVfc31cY29udHJvbGxlcjsKCgpjbGFzcyB7YXBwTmFtZV91fSBleHRlbmRzIFxvd29mcmFtZVxhcHBsaWNhdGlvblxDb250cm9sbGVyQmFzZQp7CglwdWJsaWMgZnVuY3Rpb24ge2FwcE5hbWVfdX0oKQoJewoJCXJldHVybiAnSGVsbG8gd29ybGQhIFRoaXMgYXBwbGljYXRpb24gd2FzIGdlbmVyYXRlZCBieSBPd09GcmFtZTo6UXVpY2tBcHBGcmFtR2VuZXJhdG9yLCB3ZWxjb21lIHRvIHVzZSBpdCA6KSc7Cgl9Cn0KPz4=')));

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