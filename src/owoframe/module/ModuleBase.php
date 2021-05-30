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
namespace owoframe\module;

abstract class ModuleBase
{
	/* @string 插件加载路径 */
	private $loadPath;
	/* @object 插件信息配置文件(JSON对象传入) | Plugin Information Configuration (Json Format Object) */
	private $moduleInfo;
	/* @bool 插件已加载值 */
	private $isEnabled = false;

	/**
	 * @method      __construct
	 * @description 实例化插件时的构造函数
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @param       string[loadPath|插件加载路径]
	 * @param       object[moduleInfo|插件信息配置文件]
	 */
	public final function __construct(string $loadPath, object $moduleInfo)
	{
		$this->loadPath   = $loadPath;
		$this->moduleInfo = $moduleInfo;
	}


	/**
	 * @method      onLoad
	 * @description 插件加载时自动调用此方法
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @return      void
	 */
	abstract public function onLoad() : void;



	/**
	 * @method      getInfos
	 * @description 获取插件信息对象
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @return      object
	 */
	public final function getInfos() : object
	{
		return $this->moduleInfo;
	}

	/**
	 * @method      getPath
	 * @description 获取插件加载路径
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @return      string
	 */
	public final function getPath() : string
	{
		return $this->loadPath;
	}

	/**
	 * @method      isEnabled
	 * @description 返回插件加载状态
	 * @author      HanskiJay
	 * @doenIn      2021-03-02
	 * @return      boolean
	 */
	public function isEnabled() : bool
	{
		return $this->isEnabled;
	}

	/**
	 * @method      setEnabled
	 * @description 设置插件加载状态为已加载
	 * @author      HanskiJay
	 * @doenIn      2021-03-02
	 */
	public function setEnabled() : void
	{
		if(!$this->isEnabled()) {
			$this->isEnabled = true;
		}
	}

	/**
	 * @method      setDisabled
	 * @description 设置插件加载状态为禁用
	 * @author      HanskiJay
	 * @doenIn      2021-03-02
	 */
	public function setDisabled() : void
	{
		if($this->isEnabled()) {
			$this->isEnabled = false;
		}
	}
}