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

use owoframe\console\Console;
use owoframe\utils\TextFormat as TF;

class HelpCommand extends \owoframe\console\CommandBase
{
    public function execute(array $params) : bool
    {
        $commands = Console::getInstance()->getCommands();
        ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
        if(count($params) <= 0) {
            $this->getLogger()->info(TF::GOLD . '---------- Registered Commands: ' . TF::GREEN . count($commands) . TF::GOLD . ' ----------');
            foreach($commands as $command => $class) {
                $this->getLogger()->info(TF::GREEN . $command . ': ' . TF::WHITE . $class->getDescription());
            }
            $this->getLogger()->info(TF::WHITE . 'Use \'' . self::getUsage() . TF::WHITE . '\' to look details.');
        } else {
            $command = strtolower(array_shift($params));
            if(!isset($commands[$command])) {
                $this->getLogger()->info(TF::RED . 'Command ' . TF::GOLD . $command . TF::RED . ' does not exists!');
            } else {
                $command = $commands[$command];
                $this->getLogger()->info(TF::WHITE . '---[Details@' . TF::GREEN . $command->getName() . TF::WHITE . ']---');
                $this->getLogger()->info(TF::WHITE . 'CommandName: ' . $command->getName());
                $this->getLogger()->info(TF::WHITE . 'AliasName:   ' . implode(', ', $command->getAliases()));
                $this->getLogger()->info(TF::WHITE . 'Usage:       ' . $command->getUsage());
                $this->getLogger()->info(TF::WHITE . 'Description: ' . $command->getDescription());
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
        return TF::AQUA . 'php owo help ' . TF::GOLD . '[<string> commandName]';
    }
}