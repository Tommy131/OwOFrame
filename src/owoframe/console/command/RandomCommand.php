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

use owoframe\utils\Str;

class RandomCommand extends \owoframe\console\CommandBase
{
    public function execute(array $params) : bool
    {
        $type = array_shift($params);

        switch($type) {
            default:
                $this->getLogger()->error('Please give a type!');
                return false;
            break;

            case '-s':
            case 'str':
            case 'string':
                $length = array_shift($params) ?? 12;
                $len = 0;
                $max = ask('Please give a maximum random count:', 1, true);
                while($len < (int) $max) {
                    $string = Str::randomString((int) $length);
                    $this->getLogger()->info("Random String=§3{$string}§w | Length=§3{$length}");
                    $len++;
                }
            break;

            case '-n':
            case 'num':
            case 'number':
                $len = 0;
                $set = [];
                $count = array_shift($params) ?? 1;
                $max = ask('Please give a maximum number:', 99999, true);
                while($len < (int) $count) {
                    $set[] = mt_rand(0, (int) $max);
                    $len++;
                }
                $set = implode('§w, §3', $set);
                $this->getLogger()->info("Random number(s): §3{$set}§w | Length=§3{$len}");
            break;
        }
        return true;
    }

    public static function getAliases() : array
    {
        return ['-r', '-random'];
    }

    public static function getName() : string
    {
        return 'random';
    }

    public static function getDescription() : string
    {
        return 'Random a string with specified length or random an number.';
    }

    public function sendUsage() : void
    {
        $this->getLogger()->info('Usage: ' . self::getUsage() . ' -s (length: integer) to random a string');
        $this->getLogger()->info('Usage: ' . self::getUsage() . ' -n (count: integer) number(s) from 0-99999 (default maximum: 99999)');
    }
}