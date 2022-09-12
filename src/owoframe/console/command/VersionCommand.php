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

class VersionCommand extends \owoframe\console\CommandBase
{
    public function execute(array $params) : bool
    {
        $this->getLogger()->info("Welcome to use OwOFrame :) Current version is: " . FRAME_VERSION);
        return true;
    }

    public static function getAliases() : array
    {
        return ['v', '-v', '-ver'];
    }

    public static function getName() : string
    {
        return 'version';
    }

    public static function getDescription() : string
    {
        return 'Look the version the OwOFrame.';
    }
}