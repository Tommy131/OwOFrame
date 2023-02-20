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
 * @LastEditTime : 2023-02-20 22:02:07
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;


use ZipArchive;
use owoframe\console\CommandBase;

class AddExampleModuleCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $path = \owo\module_path('');
        $to   = 'ExampleModule.zip';
        if(!is_dir($path . 'example') && copy(\owo\owo_path('module/example.zip'), $path . $to)) {
            if(!class_exists(ZipArchive::class) && !extension_loaded('zip')) {
                $this->getLogger()->notice("Added §3{$to}§6 in path §3{$path}§6, ZipArchive::class not found, you have to unzip by yourself.");
            } else {
                $zip = new ZipArchive;
                $zip->open($path. $to);
                $zip->extractTo($path);
                $zip->close();
                unlink($path. $to);
                $this->getLogger()->success("Added §3{$to}§5 in path §3{$path}§5 successfully.");
            }
        } else {
            $this->getLogger()->error('Error! Cannot add example module.');
        }
        return true;
    }

    public static function getAliases() : array
    {
        return ['addm', '-addm'];
    }

    public static function getName() : string
    {
        return 'addmodule';
    }

    public static function getDescription() : string
    {
        return 'Add a example module.';
    }
}
?>