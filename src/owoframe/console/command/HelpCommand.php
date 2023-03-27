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
 * @LastEditTime : 2023-02-15 19:21:36
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\console\CommandBase;
use owoframe\console\Console;
use owoframe\utils\TextColorOutput as TCO;

class HelpCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $commands = Console::getInstance()->getCommands();
        ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
        if(count($params) <= 0) {
            $this->getLogger()->info(TCO::GOLD . '---------- Registered Commands: ' . TCO::GREEN . count($commands) . TCO::GOLD . ' ----------');
            foreach($commands as $command => $class) {
                $this->getLogger()->info(TCO::GREEN . $command . ': ' . TCO::WHITE . $class->getDescription());
            }
            $this->getLogger()->info(TCO::WHITE . 'Use \'' . self::getUsage() . TCO::WHITE . '\' to look details.');
        } else {
            $command = strtolower(array_shift($params));
            if(!isset($commands[$command])) {
                $this->getLogger()->info(TCO::RED . 'Command ' . TCO::GOLD . $command . TCO::RED . ' does not exists!');
            } else {
                $command = $commands[$command];
                $this->getLogger()->info(TCO::WHITE . '---[Details@' . TCO::GREEN . $command->getName() . TCO::WHITE . ']---');
                $this->getLogger()->info(TCO::WHITE . 'CommandName: ' . $command->getName());
                $this->getLogger()->info(TCO::WHITE . 'AliasName  : ' . implode(', ', $command->getAliases()));
                $this->getLogger()->info(TCO::WHITE . 'Description: ' . $command->getDescription());
                $this->getLogger()->info(TCO::WHITE . 'Usage      : ' . $command->getUsage());
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
        return TCO::AQUA . parent::getUsage() . TCO::GOLD . ' [string:commandName]';
    }
}
?>