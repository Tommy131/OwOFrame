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

class AppCheckCommand extends \owoframe\console\CommandBase
{
    public function execute(array $params) : bool
    {
        $appName = array_shift($params);
        if(empty($appName)) {
            return false;
        }
        $appName = strtolower($appName);
        $appPath = APP_PATH . $appName . DIRECTORY_SEPARATOR;
        $class   = '\\application\\' . $appName . '\\' . ucfirst($appName) . 'App';

        if(is_dir($appPath) && class_exists($class)) {
            $this->getLogger()->success("Application '{$appName}' is exists.");
            $this->getLogger()->info("----------[INFO-LIST]'{$appName}'----------");
            $this->getLogger()->info('Author: ' . $class::getAuthor());
            $this->getLogger()->info('Description: ' . $class::getDescription());
            $this->getLogger()->info('Version: ' . $class::getVersion());
            return true;
        }
        $this->getLogger()->info("Application '{$appName}' does not exists.");
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

    public function sendUsage() : void
    {
        $this->getLogger()->info('Please enter a valid appName. Usage: ' . self::getUsage() . ' [string:appName]');
    }
}