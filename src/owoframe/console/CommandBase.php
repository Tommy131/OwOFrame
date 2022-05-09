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

use owoframe\utils\Logger;

abstract class CommandBase
{
	/**
	 * 日志记录容器实例
	 *
	 * @var Logger
	 */
	private $logger;

	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * 触发该指令时调用此方法执行指令
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @param  array      $params 传入的参数
	 * @return boolean
	 */
	abstract public function execute(array $params) : bool;

	/**
	 * 返回该指令的所有别名
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @return array
	 */
	abstract public static function getAliases() : array;

	/**
	 * 返回指令名称
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @return string
	 */
	abstract public static function getName() : string;

	/**
	 * 返回至零点描述
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @return string
	 */
	abstract public static function getDescription() : string;

	/**
	 * 返回使用描述
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @return string
	 */
	public static function getUsage() : string
	{
		return 'php owo ' . static::getName();
	}

	/**
	 * 给出加载器获悉是否自动加载并注册此指令
	 *
	 * @author HanskiJay
	 * @since  2021-01-26
	 * @return boolean
	 */
	public static function autoLoad() : bool
	{
		return true;
	}

	/**
	 * 返回日志记录容器实例
	 *
	 * @author HanskiJay
	 * @since  2022-05-09
	 * @return Logger
	 */
	public function getLogger() : Logger
	{
		return $this->logger;
	}
}