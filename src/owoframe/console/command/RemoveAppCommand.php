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
 * @LastEditTime : 2023-02-20 05:56:08
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\console\CommandBase;
use owoframe\object\Pipe;
use owoframe\System;

class RemoveAppCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $appName = array_shift($params);
        if(empty($appName)) {
            $this->getLogger()->info('Please enter a valid appName. Usage: ' . self::getUsage() . ' [string:appName]');
            return false;
        }

        if(!System::hasApplication($appName)) {
            $this->getLogger()->error("Cannot find appName called '{$appName}'!");
            return true;
        }

        \owo\pipe_ask('ARE YOU SURE THAT YOU WANT TO DELETE/REMOVE THIS APPLICATION? THIS OPERATION IS IRREVERSIBLE! [Y/N]', ['y', 'Y', 'yes', 'YES'])
        ->do(function(Pipe $object) use ($appName) {
            if($object->isContinue()) {
                $this->getLogger()->warning('Now will remove this application forever...');
                if(\owo\remove_dir(\owo\application_path(strtolower($appName)))) {
                    $this->getLogger()->success("Removed Application '{$appName}' successfully.");
                } else {
                    $this->getLogger()->error('Somewhere was wrong that cannot remove this application!');
                }
            } else {
                $this->getLogger()->info('Cancelled.');
            }
        });
        return true;
    }

    public static function getAliases() : array
    {
        return ['rma', '-rma'];
    }

    public static function getName() : string
    {
        return 'removeapp';
    }

    public static function getDescription() : string
    {
        return 'Look the version the OwOFrame.';
    }
}