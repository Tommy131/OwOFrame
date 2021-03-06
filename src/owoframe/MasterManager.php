<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2021 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Tommy131)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	* GitHub: https://github.com/Tommy131

************************************************************************/

declare(strict_types=1);
namespace owoframe;

use owoframe\contract\Manager;
use owoframe\helper\BootStraper as BS;
use owoframe\http\HttpManager as Http;

final class MasterManager extends Container implements Manager
{
	/* @array */
	protected $bind =
	[
		'http'    => Http::class,
		'unknown' => null
	];



	public function __construct()
	{
		if(!BS::isRunning()) {
			BS::initializeSystem();
		}
	}


	public function stop() : void
	{
		// TODO: 结束任务相关;
	}

	/**
	 * @method      bind
	 * @description 绑定到容器绑定标识
	 * @author      HanskiJay
	 * @doenIn      2021-03-05
	 * @param       string      $bindTag  绑定标识
	 * @param       mixed       $concrete interface@Manager
	 * @return      void
	 */
	public function bind(string $bindTag, $concrete) : void
	{
		if(!$concrete instanceof Manager) {
			return;
		}
		parent::bind($bindTag, $concrete);
	}

	/**
	 * @method      getManager
	 * @description 返回选择的管理器
	 * @author      HanskiJay
	 * @doenIn      2021-03-04
	 * @param       string      $bindTag 绑定标识
	 * @return      @Manager
	 */
	public function getManager(string $bindTag) : Manager
	{
		return $this->make($bindTag ?? 'unknown');
	}

	/**
	 * @method      isRunning
	 * @description 返回系统运行状态
	 * @author      HanskiJay
	 * @doenIn      2021-03-04
	 * @return      boolean
	 */
	public function isRunning() : bool
	{
		return BS::isRunning();
	}
}