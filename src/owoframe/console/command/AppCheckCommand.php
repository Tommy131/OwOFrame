<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-15 18:49:38
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-15 18:53:25
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\System;
use owoframe\console\CommandBase;

class AppCheckCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $appName = array_shift($params);
        if(empty($appName)) {
            return false;
        }
        $class = System::getApplication(strtolower($appName));

        if($class) {
            $this->getLogger()->success("Application '{$appName}' exists.");
            $this->getLogger()->info("----------[INFO-LIST]'{$appName}'----------");
            $this->getLogger()->info('Author:      ' . $class::getAuthor());
            $this->getLogger()->info('Description: ' . $class::getDescription());
            $this->getLogger()->info('Version:     ' . $class::getVersion());
            return true;
        }
        $this->getLogger()->info("Application '{$appName}' does not exists.");
        return true;
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
        return 'Check if the app exists.';
    }

    public static function getUsage() : string
    {
        return parent::getUsage() . ' [string:appName]';
    }
}
?>