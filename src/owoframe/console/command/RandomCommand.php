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

class RandomCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $type = array_shift($params);

        switch($type) {
            default:
                $this->getLogger()->error('Please give a type!');
            return false;

            case '-s':
            case 'str':
            case 'string':
                $length = array_shift($params) ?? 12;
                $len = 0;
                $max = \owo\ask('Please give a maximum random count:', 1);
                while($len < (int) $max) {
                    $string = \owo\random_string((int) $length);
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
                $max = \owo\ask('Please give a maximum number:', 99999);
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

    public static function getUsage() : string
    {
        return 'Usage: ' . parent::getUsage() . ' -s (length: integer) to random a string (default length: 12)' . PHP_EOL .
        'Usage: ' . parent::getUsage() . ' -n (count: integer) number(s) from 0-99999 (default maximum: 99999)';
    }
}
?>