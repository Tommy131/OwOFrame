<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com

************************************************************************/

namespace backend\system\console;

class Console
{
	/* @string 命名空间 */
	private static $namespace = '\\backend\\system\\console\\command\\';
	/* @array 指令池 */
	private $commandPool = [];
	/* @array 指令别名存放 */
	private $usedAliases = [];


	/**
	 * @method      __construct
	 * @description 实例化Console类的构造函数
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 */
	public function __construct()
	{
		$cmdPath  = __DIR__ . DIRECTORY_SEPARATOR . 'command' . DIRECTORY_SEPARATOR;
		$dirArray = scandir($cmdPath);
		unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);

		foreach($dirArray as $fileName) {
			$class = @array_shift(explode('.', $fileName));
			if(class_exists(($class = self::$namespace . $class))) {
				$commandString = strtolower($class::getName());
				if(!$class::autoLoad() || isset($this->commandPool[$commandString])) continue;
				if(count(array_intersect($class::getAliases(), $this->usedAliases)) >= 1) {
					\OwOBootStrap\logger("Cannot register command '".$class::getName()."' because the alias name has been registered in somewhere.", 'OwOCMD', 'ERROR');
					return;
				}
				$class = $this->commandPool[$commandString] = new $class();
				$this->usedAliases = array_merge($class::getAliases(), $this->usedAliases);
			}
		}
	}

	/**
	 * @method      monitor
	 * @description 监听指令传入
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      void
	 */
	public function monitor(array $input = []) : void
	{
		array_shift($input);
		if(count($input) <= 0) return;
		$inputCommand = strtolower(array_shift($input));

		if(($command = $this->getCommand($inputCommand)) === null) {
			foreach($this->commandPool as $command => $tmp) {
				if(in_array($inputCommand, $tmp->getAliases())) {
					$command = $tmp;
					break;
				}
			}
		}

		if($command instanceof CommandBase) {
			if(!$command->execute($input)) {
				\OwOBootStrap\logger("Command '{$inputCommand}' may not execute successfully, please check the issue.", 'OwOCMD');
			}
		} else {
			\OwOBootStrap\logger("Command '{$inputCommand}' not found, please use 'php owo help' to  check the details.", 'OwOCMD');
		}
	}
	/**
	 * @method      getCommand
	 * @description 获取指令
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @param       string[commandString|指令]
	 * @return      null or class@CommandBase
	 */
	public function getCommand(string $commandString) : ?CommandBase
	{
		return $this->commandPool[strtolower($commandString)] ?? null;
	}
	/**
	 * @method      hasCommand
	 * @description 判断指令是否存在
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      boolean
	 */
	public function hasCommand() : bool
	{
		return isset($this->commandPool[strtolower($commandString)]);
	}
}