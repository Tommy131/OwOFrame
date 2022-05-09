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

use owoframe\MasterManager;
use owoframe\utils\Logger;
use owoframe\utils\TextFormat as TF;

class Console implements \owoframe\interfaces\Unit
{
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

	/**
	 * 日至记录容器实例
	 *
	 * @var Logger
	 */
	private $logger;


	/**
	 * 实例化Console类的构造函数
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 */
	public function __construct()
	{
		$this->logger = MasterManager::getInstance()->getUnit('logger');
		$cmdPath  = __DIR__ . DIRECTORY_SEPARATOR . 'command' . DIRECTORY_SEPARATOR;
		$dirArray = scandir($cmdPath);
		unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);

		foreach($dirArray as $fileName) {
			$class = @array_shift(explode('.', $fileName));
			if(class_exists(($class = self::$namespace . $class))) {
				$commandString = strtolower($class::getName());
				if(!$class::autoLoad() || $this->hasCommand($commandString)) continue;
				if(count(array_intersect($class::getAliases(), $this->usedAliases)) >= 1) {
					$this->logger->error(TF::RED."Cannot register command '".TF::GOLD.$class::getName().TF::RED."' because the alias name has been registered in somewhere.");
					return;
				}
				$class = new $class($this->logger);
				$this->registerCommand($commandString, $class);
				$this->usedAliases = array_merge($class::getAliases(), $this->usedAliases);
			}
		}
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
		array_shift($input);
		if(count($input) <= 0) {
			$this->logger->info("Hi there, welcome to use OwOFrame :) You can use command like '".TF::GOLD."php owo help".TF::WHITE."' to display the Helper.");
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
				$this->logger->debug("Command '{$inputCommand}' may not execute successfully, please check the issue.");
			}
		} else {
			$this->logger->debug("Command '{$inputCommand}' not found, please use '".TF::GOLD."php owo help".TF::GRAY."' to check the details.");
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
	 * @param  string      $commandString
	 * @param  CommandBase $class
	 * @return boolean
	 */
	public function registerCommand(string $commandString, CommandBase $class) : bool
	{
		if(!$this->hasCommand($commandString)) {
			$this->commandPool[strtolower($commandString)] = $class;
			return true;
		}
		return false;
	}
}