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
 * @LastEditTime : 2023-02-15 19:04:39
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\console\CommandBase;
use owoframe\utils\TextColorOutput as TCO;

class CheckUpdateCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $this->getLogger()->notice("Current version is: " . OWO_VERSION . ', checking update......');
        $raw = file_get_contents('https://raw.githubusercontent.com/Tommy131/Tommy131/main/VERSION.owo');
        if(!$raw) {
            $this->getLogger()->error('Unknown Error caused, no data received.');
        }
        $compare = version_compare($raw, OWO_VERSION);
        if($compare === 0) {
            $this->getLogger()->success('Currently is the newest version.');
        } else {
            if($compare === 1) {
                $message = 'Outdated version! Please go to the GitHub ' . TCO::YELLOW . GITHUB_PAGE . TCO::AQUA . ' or use command ' . TCO::YELLOW . 'git pull'. TCO::AQUA . ' to update! ';
            }
            elseif($compare === -1) {
                $message = TCO::LIGHT_RED . 'Your version is too high! What have you done?';
            } else {
                $message = TCO::LIGHT_RED . $compare;
            }
            $this->getLogger()->notice($message);
        }
        return true;
    }

    public static function getAliases() : array
    {
        return ['u', '-u'];
    }

    public static function getName() : string
    {
        return 'update';
    }

    public static function getDescription() : string
    {
        return 'Check the newest version for the OwOFrame.';
    }
}
?>