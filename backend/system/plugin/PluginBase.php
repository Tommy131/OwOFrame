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
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com
	
************************************************************************/

declare(strict_types=1);
namespace backend\system\plugin;

abstract class PluginBase
{
	/* @string 插件加载路径 */
	private $loadPath;
	/* @object 插件信息配置文件(JSON对象传入) | Plugin Information Configuration (Json Format Object) */
	private $pluginInfo;
	

	/**
	 * @method      __construct
	 * @description 实例化插件时的构造函数
	 * @author      HanskiJay
	 * @doenIn      2021-01-23
	 * @param       string[loadPath|插件加载路径]
	 * @param       object[pluginInfo|插件信息配置文件]
	 */
	public final function __construct(string $loadPath, object $pluginInfo)
	{
		$this->loadPath   = $loadPath;
		$this->pluginInfo = $pluginInfo;
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
		return $this->pluginInfo;
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
}