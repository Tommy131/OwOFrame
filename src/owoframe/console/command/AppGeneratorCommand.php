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
 * @LastEditTime : 2023-02-20 05:46:56
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use owoframe\console\CommandBase;

class AppGeneratorCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        $appName = array_shift($params);
        if(empty($appName)) {
            return false;
        }
        $appName    = strtolower($appName);
        $upAppName  = ucfirst($appName);
        $appPath    = \owo\application_path($appName, true);
        $ctlPath    = $appPath . 'controller' . DIRECTORY_SEPARATOR;
        $viewPath   = $appPath . 'view' . DIRECTORY_SEPARATOR;
        $staticPath = $viewPath . 'static' . DIRECTORY_SEPARATOR;

        if(is_dir($appPath)) {
            $this->getLogger()->info("Application '{$appName}' may exists, please delete/rename/move it and then use this command.");
            return true;
        } else {
            mkdir($appPath, 755, true);
            mkdir($ctlPath, 755, true);
            if(!is_dir($viewPath))   mkdir($viewPath, 755, true);
            if(!is_dir($staticPath)) mkdir($staticPath, 755, true);

            // Make application main info class file
            $appFile = $appPath . $upAppName . 'App.php';
            file_put_contents($appFile, str_replace(['applicationName', 'className'], [$appName, $upAppName . 'App'], file_get_contents(\owo\s_template_path('DefaultAppTemplate.php'))));

            // Make application default controller file
            $controllerFile = $ctlPath . $upAppName . '.php';
            file_put_contents($controllerFile, str_replace(['applicationName', 'className'], [$appName, $upAppName], file_get_contents(\owo\s_template_path('DefaultControllerTemplate.php'))));

            if(is_dir($appPath) && is_file($appFile) && is_file($controllerFile)) {
                $this->getLogger()->success("Generated empty AppFrame '{$upAppName}' successfully. Please check the app path '{$appPath}' to develop it.");
                return true;
            } else {
                $this->getLogger()->error('An unknown error caused that cannot generate this empty AppFrame!');
                return true;
            }
        }
    }

    public static function getAliases() : array
    {
        return ['ag', '-n', 'new'];
    }

    public static function getName() : string
    {
        return 'newapp';
    }

    public static function getDescription() : string
    {
        return 'To generate a Application demo.';
    }

    public static function getUsage() : string
    {
        return parent::getUsage() . ' [string:appName]';
    }
}
?>