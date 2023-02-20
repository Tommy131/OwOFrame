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
 * @Date         : 2023-02-15 18:23:55
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-15 18:43:03
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console;



use owoframe\System;
use owoframe\utils\TextColorOutput as TCO;

class Console
{
    /**
     * 单例实例
     *
     * @var Console
     */
    private static $instance = null;

    /**
     * 指令池
     *
     * @access private
     * @var array
     */
    private $commandPool = [];

    /**
     * 指令别名存放
     *
     * @access private
     * @var array
     */
    private $usedAliases = [];


    private function __construct()
    {
    }

    /**
     * 初始化控制台
     *
     * @return Console
     */
    public function init() : Console
    {
        $dir = scandir(__DIR__ . DIRECTORY_SEPARATOR . 'command' . DIRECTORY_SEPARATOR);
        unset($dir[array_search('.', $dir)], $dir[array_search('..', $dir)]);

        foreach($dir as $fileName)
        {
            $class = explode('.', $fileName);
            $class = __NAMESPACE__ . '\\command\\' . array_shift($class);
            if(is_a($class, CommandBase::class, true)) {
                $commandString = strtolower($class::getName());
                if(!$class::autoLoad() || $this->hasCommand($commandString)) continue;
                if(count(array_intersect($class::getAliases(), $this->usedAliases)) >= 1) {
                    System::getMainLogger()->error(TCO::RED."Cannot register command '".TCO::GOLD.$class::getName().TCO::RED."' because the alias name has been registered in somewhere.");
                    return $this;
                }
                $class = new $class();
                $this->registerCommand($class);
            }
        }
        return $this;
    }

    /**
     * 监听指令传入
     *
     * @return void
     */
    public function monitor(array $input = []) : void
    {
        // Remove first parameter 'php'
        array_shift($input);
        if(count($input) <= 0) {
            System::getMainLogger()->info("Hi there, welcome to use OwOFrame :) You can use command like '".TCO::GOLD."php owo help".TCO::WHITE."' to display the Helper.");
            return;
        }
        $inputCommand = strtolower(array_shift($input));

        if(($command = $this->getCommand($inputCommand)) === null) {
            foreach($this->commandPool as $command => $class) {
                if(in_array($inputCommand, $class->getAliases())) {
                    $command = $class;
                    break;
                }
            }
        }

        if($command instanceof CommandBase) {
            if(!$command->execute($input)) {
                System::getMainLogger()->info($command->getUsage());
                // System::getMainLogger()->debug("Command '{$inputCommand}' may not execute successfully, please check the issue.");
            }
        } else {
            System::getMainLogger()->debug("Command '{$inputCommand}' not found, please use '".TCO::GOLD."owo help".TCO::GRAY."' to check the details.");
        }
    }

    /**
     * 返回已注册的指令列表
     *
     * @return array
     */
    public function getCommands() : array
    {
        return $this->commandPool;
    }

    /**
     * 判断指令是否存在
     *
     * @param  string  $commandString
     * @return boolean
     */
    public function hasCommand(string $commandString) : bool
    {
        return isset($this->commandPool[strtolower($commandString)]);
    }

    /**
     * 获取指令
     *
     * @param  string           $commandString
     * @return CommandBase|null
     */
    public function getCommand(string $commandString) : ?CommandBase
    {
        return $this->commandPool[strtolower($commandString)] ?? null;
    }

    /**
     * 注册指令
     *
     * @param  CommandBase $class
     * @return boolean
     */
    public function registerCommand(CommandBase $class) : bool
    {
        if(!$this->hasCommand($class::getName()) && $class::autoLoad()) {
            $this->commandPool[strtolower($class::getName())] = $class;
            $this->usedAliases = array_merge($class::getAliases(), $this->usedAliases);
            return true;
        }
        return false;
    }

    /**
     * 注销指令
     *
     * @param  string  $commandString
     * @return boolean
     */
    public function unregisterCommand(string $commandString) : bool
    {
        if($this->hasCommand($commandString)) {
            $class =& $this->commandPool[$commandString];
            // 求数组差集
            $this->usedAliases = array_diff($this->usedAliases, $class::getAliases());
            unset($class);
        }
        return false;
    }

    /**
     * 返回单例实例
     *
     * @return Console
     */
    public static function getInstance() : Console
    {
        if(!static::$instance instanceof Console) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}
?>