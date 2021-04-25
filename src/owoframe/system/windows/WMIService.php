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
	 * @method      getCPURawInfo
	 * @description 获取CPU信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getCPURawInfo(bool $forceUpdate = false) : array
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
	 * @method      getCPUInfo
	 * @description 返回格式化后的CPU信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-25
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      string
	 */
	public function getCPUInfo(bool $forceUpdate = false) : string
	{
		$cpuInfo  = $this->getCPURawInfo($forceUpdate);
		$template = '[CPU%d] %s (%dH%dT)';
		if(($num = count($cpuInfo)) > 1) {
			$result = '';
			for($i = 0; $i <= ($num - 1); $i++) {
				$result .= sprintf($template, $i, trim($cpuInfo['Name']), $cpuInfo['NumberOfCores'], $cpuInfo['ThreadCount']) . '<br/>';
			}
			$cpuInfo = $result;
		} else {
			$cpuInfo = array_shift($cpuInfo);
			$cpuInfo = sprintf($template, 0, trim($cpuInfo['Name']), $cpuInfo['NumberOfCores'], $cpuInfo['ThreadCount']);
		}
		return $cpuInfo;
	}

	/**
	 * @method      getMemoryRawInfo
	 * @description 获取内存信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getMemoryRawInfo(bool $forceUpdate = false) : array
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
	 * @method      getMemoryInfo
	 * @description 获取格式化后的内存信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-25
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      string
	 */
	public function getMemoryInfo(bool $forceUpdate = false) : string
	{
		$memoryInfo = $this->getMemoryRawInfo();
		$template   = '%s %d GB (%d x %d MB) S:%dMHz@CS:%dMHz';
		if(($num = count($memoryInfo)) > 1) {
			$totalSize = 0;
			for($i = 0; $i <= ($num - 1); $i++) {
				$totalSize += $memoryInfo[$i]['TypeDetail'] - 128;
			}
			$memory     = array_shift($memoryInfo);
			$memoryInfo = sprintf($template, $memory['Name'], ($totalSize / 1024), $num, ($totalSize / $num), $memory['Speed'], $memory['ConfiguredClockSpeed']);
		} else {
			$memoryInfo = array_shift($memoryInfo);
			$memoryInfo = sprintf($template, $memoryInfo['Name'], ($totalSize / 1024), $num, ($totalSize / $num), $memoryInfo['Speed'], $memoryInfo['ConfiguredClockSpeed']);
		}
		return $memoryInfo;
	}

	/**
	 * @method      getDiskRawInfo
	 * @description 获取磁盘信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-24
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getDiskRawInfo(bool $forceUpdate = false) : array
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
	 * @method      getLogicalDiskRawInfo
	 * @description 获取硬盘逻辑分区信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-25
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getLogicalDiskRawInfo(bool $forceUpdate = false) : array
	{
		if(isset($this->data['logicalDisk']) && !$forceUpdate) {
			return $this->data['logicalDisk'];
		}
		$diskArr = [];
		$wmi     = $this->getRealWMI('Win32_LogicalDisk');
		foreach($wmi as $num => $disk) {
			$diskArr[$num]['Caption']     = $disk->Caption;
			$diskArr[$num]['Description'] = str2UTF8($disk->Description);
			$diskArr[$num]['FreeSpace']   = $disk->FreeSpace;
			$diskArr[$num]['Size']        = $disk->Size;
			$diskArr[$num]['VolumeName']  = str2UTF8($disk->VolumeName);
		}
		return $this->data['logicalDisk'] = $diskArr;
	}

	public function getDiskInfo(array $exceptionCallParam = [], &$call = []) : array
	{
		$diskInfo = $this->getLogicalDiskRawInfo();
		$template = '[Disk%d][%s] %s (%s) %d GB/ %d GB (%s)';
		if(($num = count($diskInfo)) > 1) {
			$result = [];
			for($i = 0; $i <= ($num - 1); $i++) {
				$usedSpace   = $diskInfo[$i]['Size'] - $diskInfo[$i]['FreeSpace'];
				$usedPercent = round($usedSpace / $diskInfo[$i]['Size'] * 100, 2);
				$usedSpace   = $usedSpace / 1024 / 1024 / 1024;
				$totalSize   = $diskInfo[$i]['Size'] / 1024 / 1024 / 1024;
				$result[]    = sprintf($template, $i, $diskInfo[$i]['Caption'], $diskInfo[$i]['VolumeName'], $diskInfo[$i]['Description'], $usedSpace, $totalSize, $usedPercent . '%');
				if(!empty($exceptionCallParam)) {
					foreach($exceptionCallParam as $param) {
						if(isset(${$param})) {
							$call[$i][$param] = ${$param};
						}
					}
				}
			}
			$diskInfo = $result;
		} else {
			$diskInfo    = array_shift($diskInfo);
			$usedSpace   = $diskInfo['Size'] - $diskInfo['FreeSpace'];
			$usedPercent = round($usedSpace / $diskInfo['Size'] * 100, 2) . '%';
			$usedSpace   = $usedSpace / 1024 / 1024 / 1024;
			$totalSize   = $diskInfo['Size'] / 1024 / 1024 / 1024;
			$diskInfo    = sprintf($template, 0, $diskInfo['Caption'], $diskInfo['VolumeName'], $diskInfo['Description'], $usedSpace, $totalSize, $usedPercent . '%');
			if(!empty($exceptionCallParam)) {
				foreach($exceptionCallParam as $param) {
					if(isset(${$param})) {
						$call[0][$param] = ${$param};
					}
				}
			}
		}
		return $diskInfo;
	}

	/**
	 * @method      getServerRawInfo
	 * @description 获取系统基本信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-25
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getServerRawInfo(bool $forceUpdate = false) : array
	{
		if(isset($this->data['server']) && !$forceUpdate) {
			return $this->data['server'];
		}
		$infoArr = [];
		$wmi     = $this->getRealWMI('Win32_OperatingSystem');
		foreach($wmi as $info) {
			$infoArr['LastBootUpTime']         = $info->LastBootUpTime;
			$infoArr['TotalVisibleMemorySize'] = $info->TotalVisibleMemorySize;
			$infoArr['FreePhysicalMemory']     = $info->FreePhysicalMemory;
			$infoArr['Caption']                = str2UTF8($info->Caption);
			$infoArr['CSDVersion']             = $info->CSDVersion;
			$infoArr['SerialNumber']           = $info->SerialNumber;
			$infoArr['InstallDate']            = $info->InstallDate;
		}
		return $this->data['server'] = $infoArr;
	}

	/**
	 * @method      getServerInfo
	 * @description 获取格式化后的系统基本信息
	 * @author      HanskiJay
	 * @doenIn      2021-04-25
	 * @param       bool|boolean  $forceUpdate 强制更新信息
	 * @return      array
	 */
	public function getServerInfo(bool $forceUpdate = false) : array
	{
		// 系统基本信息;
		$serverInfo  = $this->getServerRawInfo($forceUpdate);
		$runningTime = time() - strtotime(substr($serverInfo['LastBootUpTime'], 0, 14));
		$days        = floor($runningTime / (24 * 3600));
		$hours       = floor(($runningTime - $days) / 3600);
		$minutes     = floor(($runningTime - ($days * 24 * 3600) - ($hours * 3600)) / 60);
		$seconds     = floor($runningTime - ($days * 24 * 3600) - ($hours * 3600) - ($minutes * 60) - 2);

		// 格式化输出显示;
		$seconds     = ($seconds < 0) ? (60 + $seconds) : $seconds;
		$days        = ($days < 10) ? ('0' . $days) : $days;
		$hours       = ($hours < 10) ? ('0' . $hours) : $hours;
		$minutes     = ($minutes < 10) ? ('0' . $minutes) : $minutes;
		$seconds     = ($seconds < 10) ? ('0' . $seconds) : $seconds;
		$runningTime = "{$days}天{$hours}时{$minutes}分{$seconds}秒";

		// 内存使用信息;
		$totalMemory = $serverInfo['TotalVisibleMemorySize'];
		$freeMemory  = $serverInfo['FreePhysicalMemory'];
		$usedMemory  = $totalMemory - $freeMemory;
		$usedMemoryPercent = round($usedMemory / $totalMemory * 100, 2);
		$restMemoryPercent = round($freeMemory / $totalMemory * 100, 2);
		$totalMemory = round($totalMemory / 1024, 2) . ' MB';
		$freeMemory  = round($freeMemory / 1024, 2) . ' MB';
		$usedMemory  = round($usedMemory / 1024, 2) . ' MB';
		$memoryUsageInfo  = '总内存: ' . $totalMemory . ' | 已使用: ' . $usedMemory . ' | 剩余: ' . $freeMemory;
		return [$runningTime, $memoryUsageInfo, $usedMemoryPercent, $restMemoryPercent];
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