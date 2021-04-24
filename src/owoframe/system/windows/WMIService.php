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
	* GitHub: https://github.com/Tommy131
	
************************************************************************/

declare(strict_types=1);
namespace owoframe\system\windows;

class WMIService extends WMI
{
	/* @array 数据存储 */
	protected $data = [];

	/**
	 * @method      getCPUInfo
	 * @description 获取CPU信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getCPUInfo(bool $forceUpdate = false) : array
	{
		if(isset($this->data['cpu']) && !$forceUpdate) {
			return $this->data['cpu'];
		}

		$cpuArr = [];
		$wmi    = $this->getRealWMI('Win32_Processor');
		foreach($wmi as $num => $cpu) {
			$cpuArr[$num]['DeviceID']          = $cpu->DeviceID;
			$cpuArr[$num]['Manufacturer']      = $cpu->Manufacturer;
			$cpuArr[$num]['Name']              = $cpu->Name;
			$cpuArr[$num]['DataWidth']         = $cpu->DataWidth;
			$cpuArr[$num]['CurrentClockSpeed'] = $cpu->CurrentClockSpeed;
			$cpuArr[$num]['SocketDesignation'] = $cpu->SocketDesignation;
			$cpuArr[$num]['Version']           = str2UTF8($cpu->Version);
			$cpuArr[$num]['NumberOfCores']     = $cpu->NumberOfCores;
			$cpuArr[$num]['ThreadCount']       = $cpu->ThreadCount;

			$cpuArr[$num]['L2CacheSize']       = $cpu->L2CacheSize;
			$cpuArr[$num]['L3CacheSize']       = $cpu->L3CacheSize;
		}
		return $this->data['cpu'] = $cpuArr;
	}

	/**
	 * @method      getMemoryInfo
	 * @description 获取内存信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getMemoryInfo(bool $forceUpdate = false) : array
	{
		if(isset($this->data['memory']) && !$forceUpdate) {
			return $this->data['memory'];
		}
		$memoryArr = [];
		$wmi       = $this->getRealWMI('Win32_PhysicalMemory');
		foreach($wmi as $num => $memory) {
			$memoryArr[$num]['DeviceLocator']        = $memory->DeviceLocator;
			$memoryArr[$num]['Name']                 = str2UTF8($memory->Name);
			$memoryArr[$num]['Speed']                = $memory->Speed;
			$memoryArr[$num]['ConfiguredClockSpeed'] = $memory->ConfiguredClockSpeed;
			$memoryArr[$num]['TypeDetail']           = $memory->TypeDetail;
		}
		return $this->data['memory'] = $memoryArr;
	}

	/**
	 * @method      getDiskInfo
	 * @description 获取磁盘信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getDiskInfo(bool $forceUpdate = false) : array
	{
		if(isset($this->data['disk']) && !$forceUpdate) {
			return $this->data['disk'];
		}
		$diskArr = [];
		$wmi     = $this->getRealWMI('Win32_DiskDrive');
		foreach($wmi as $num => $disk) {
			$diskArr[$num]['Caption']       = $disk->Caption;
			$diskArr[$num]['InterfaceType'] = $disk->InterfaceType;
			$diskArr[$num]['Partitions']    = $disk->Partitions;
			$diskArr[$num]['Size']          = $disk->Size;
		}
		return $this->data['disk'] = $diskArr;
	}

	/**
	 * @method      getData
	 * @description 返回数据
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @return      array
	 */
	public function getData() : array
	{
		return $this->data;
	}
}