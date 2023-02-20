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
 * @LastEditTime : 2023-02-15 19:29:40
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\console\CommandBase;
use owoframe\http\WebSocket;

class WebSocketCommand extends CommandBase
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