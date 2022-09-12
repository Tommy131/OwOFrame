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
namespace owoframe\console;

use owoframe\System;
use owoframe\utils\TextFormat as TF;

class Console
{
    /**
     * 单例实例
     *
     * @var Console
     */
    private static $instance = null;

    /**
     * 命名空间
     *
     * @access private
     * @var string
     */
    private static $namespace = '\\owoframe\\console\\command\\';

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

        foreach($dir as $fileName) {
            $class = @array_shift(explode('.', $fileName));
            if(is_a($class = self::$namespace . $class, CommandBase::class, true)) {
                $commandString = strtolower($class::getName());
                if(!$class::autoLoad() || $this->hasCommand($commandString)) continue;
                if(count(array_intersect($class::getAliases(), $this->usedAliases)) >= 1) {
                    System::getLogger()->error(TF::RED."Cannot register command '".TF::GOLD.$class::getName().TF::RED."' because the alias name has been registered in somewhere.");
                    return $this;
                }
                $class = new $class();
                $this->registerCommand($class);
                $this->usedAliases = array_merge($class::getAliases(), $this->usedAliases);
            }
        }
        return $this;
    }

    /**
     * 监听指令传入
     *
     * @author HanskiJay
     * @since  2021-01-26
     * @return void
     */
    public function monitor(array $input = []) : void
    {
        // Remove first parameter 'php';
        array_shift($input);
        if(count($input) <= 0) {
            System::getLogger()->info("Hi there, welcome to use OwOFrame :) You can use command like '".TF::GOLD."php owo help".TF::WHITE."' to display the Helper.");
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
                $command->sendUsage();
                // System::getLogger()->debug("Command '{$inputCommand}' may not execute successfully, please check the issue.");
            }
        } else {
            System::getLogger()->debug("Command '{$inputCommand}' not found, please use '".TF::GOLD."php owo help".TF::GRAY."' to check the details.");
        }
    }

    /**
     * 获取指令
     *
     * @author HanskiJay
     * @since  2021-01-26
     * @param  string      $commandString 指令
     * @return CommandBase|null
     */
    public function getCommand(string $commandString) : ?CommandBase
    {
        return $this->commandPool[strtolower($commandString)] ?? null;
    }

    /**
     * 返回已注册的指令列表
     *
     * @author HanskiJay
     * @since  2021-03-06
     * @return array
     */
    public function getCommands() : array
    {
        return $this->commandPool;
    }

    /**
     * 判断指令是否存在
     *
     * @author HanskiJay
     * @since  2021-01-26
     * @param  string      $commandString 指令
     * @return boolean
     */
    public function hasCommand(string $commandString) : bool
    {
        return isset($this->commandPool[strtolower($commandString)]);
    }

    /**
     * 注册指令
     *
     * @author HanskiJay
     * @since  2021-01-26
     * @param  CommandBase $class
     * @return Console
     */
    public function registerCommand(CommandBase $class) : Console
    {
        if(!$this->hasCommand($class::getName())) {
            $this->commandPool[strtolower($class::getName())] = $class;
        }
        return $this;
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