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

abstract class CommandBase
{
	/**
	 * @method      execute
	 * @description 触发该指令时调用此方法执行指令
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @param       array[params|传入的参数]
	 * @return      boolean
	 */
	abstract public function execute(array $params) : bool;

	/**
	 * @method      getAliases
	 * @description 返回该指令的所有别名
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      array
	 */
	abstract public static function getAliases() : array;

	/**
	 * @method      getName
	 * @description 返回指令名称
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      string
	 */
	abstract public static function getName() : string;

	/**
	 * @method      getDescription
	 * @description 返回至零点描述
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      string
	 */
	abstract public static function getDescription() : string;

	/**
	 * @method      getUsage
	 * @description 返回使用描述
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      string
	 */
	public static function getUsage() : string
	{
		return 'php owo ' . static::getName();
	}

	/**
	 * @method      autoLoad
	 * @description 给出加载器获悉是否自动加载并注册此指令
	 * @author      HanskiJay
	 * @doenIn      2021-01-26
	 * @return      boolean
	 */
	public static function autoLoad() : bool
	{
		return true;
	}

}