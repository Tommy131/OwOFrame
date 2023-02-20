<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-09 22:49:48
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-10 00:01:32
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\utils\windows;



use Variant;

class SystemData extends WMI
{
    /**
     * 数据存储
     *
     * @access protected
     * @var array
     */
    protected $data = [];



    /**
     * 返回数据
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * 判断是否存在数据
     *
     * @param  string  $index
     * @return boolean
     */
    public function has(string $index) : bool
    {
        return isset($this->data[$index]);
    }

    /**
     * 返回数据
     *
     * @param  string $index
     * @return array|null
     */
    public function get(string $index) : ?array
    {
        return $this->data[$index] ?? null;
    }

    /**
     * 代理方法
     *
     * @param  string  $type
     * @param  boolean $update
     * @return Variant
     */
    public function proxy_get(string $type, bool $update = false) : Variant
    {
        static $__;
        $_ =
        [
            'cpu'         => 'Win32_Processor',
            'memory'      => 'Win32_PhysicalMemory',
            'disk'        => 'Win32_DiskDrive',
            'logicalDisk' => 'Win32_LogicalDisk',
            'server'      => 'Win32_OperatingSystem'
        ];

        if(!isset($_[$type])) {
            return null;
        }
        return (!$update && isset($__[$type])) ? $__[$type] : ($__[$type] = $this->getWMI($_[$type]));
    }

    /**
     * 获取CPU信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getCPURawInfo(bool $update = false) : array
    {
        $wmi     = $this->proxy_get('cpu', $update);
        $cpuList = [];
        foreach($wmi as $num => $cpu) {
            $cpuList[$num]['DeviceID']          = $cpu->DeviceID;
            $cpuList[$num]['Manufacturer']      = $cpu->Manufacturer;
            $cpuList[$num]['Name']              = $cpu->Name;
            $cpuList[$num]['DataWidth']         = $cpu->DataWidth;
            $cpuList[$num]['CurrentClockSpeed'] = $cpu->CurrentClockSpeed;
            $cpuList[$num]['SocketDesignation'] = $cpu->SocketDesignation;
            $cpuList[$num]['Version']           = \owo\str2UTF8($cpu->Version);
            $cpuList[$num]['NumberOfCores']     = $cpu->NumberOfCores;
            $cpuList[$num]['ThreadCount']       = $cpu->ThreadCount;
            $cpuList[$num]['L2CacheSize']       = $cpu->L2CacheSize;
            $cpuList[$num]['L3CacheSize']       = $cpu->L3CacheSize;
        }
        return $this->data['cpu'] = $cpuList;
    }

    /**
     * 返回格式化后的CPU信息
     *
     * @param  boolean $update
     * @return string
     */
    public function getCPUInfo(bool $update = false) : string
    {
        $cpuInfo  = $this->getCPURawInfo(true, $update);
        $template = '[CPU%d] %s (%dH%dT)';
        $result   = '';
        for($i = 0; $i <= count($cpuInfo) - 1; $i++) {
            $result .= sprintf($template, $i, trim($cpuInfo[$i]['Name']), $cpuInfo[$i]['NumberOfCores'], $cpuInfo[$i]['ThreadCount']) . "\n";
        }
        return $result;
    }

    /**
     * 获取内存信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getMemoryRawInfo(bool $update = false) : array
    {
        $wmi        = $this->proxy_get('memory', $update);
        $memoryList = [];
        foreach($wmi as $num => $memory) {
            $memoryList[$num]['DeviceLocator']        = $memory->DeviceLocator;
            $memoryList[$num]['Name']                 = \owo\str2UTF8($memory->Name);
            $memoryList[$num]['Speed']                = $memory->Speed;
            $memoryList[$num]['ConfiguredClockSpeed'] = $memory->ConfiguredClockSpeed;
            $memoryList[$num]['TypeDetail']           = $memory->TypeDetail;
        }
        return $this->data['memory'] = $memoryList;
    }

    /**
     * 获取格式化后的内存信息
     *
     * @return string
     */
    public function getMemoryInfo() : string
    {
        $memoryInfo = $this->getMemoryRawInfo();
        $template   = '%s %d GB (%d x %d MB) S:%dMHz@CS:%dMHz';
        $totalSize  = 0;
        $num        = count($memoryInfo);
        for($i = 0; $i <= ($num - 1); $i++) {
            $totalSize += $memoryInfo[$i]['TypeDetail'] - 128;
        }
        $memory     = array_shift($memoryInfo);
        $memoryInfo = sprintf($template, $memory['Name'], ($totalSize / 1024), $num, ($totalSize / $num), $memory['Speed'], $memory['ConfiguredClockSpeed']);
        return $memoryInfo;
    }

    /**
     * 获取磁盘信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getDiskRawInfo(bool $update = false) : array
    {
        $wmi      = $this->proxy_get('disk', $update);
        $diskList = [];
        foreach($wmi as $num => $disk) {
            $diskList[$num]['Caption']       = $disk->Caption;
            $diskList[$num]['InterfaceType'] = $disk->InterfaceType;
            $diskList[$num]['Partitions']    = $disk->Partitions;
            $diskList[$num]['Size']          = $disk->Size;
        }
        return $this->data['disk'] = $diskList;
    }

    /**
     * 获取硬盘逻辑分区信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getLogicalDiskRawInfo(bool $update = false) : array
    {
        $wmi      = $this->proxy_get('logicalDisk', $update);
        $diskList = [];
        foreach($wmi as $num => $disk) {
            $diskList[$num]['Caption']     = $disk->Caption;
            $diskList[$num]['Description'] = \owo\str2UTF8($disk->Description);
            $diskList[$num]['FreeSpace']   = $disk->FreeSpace;
            $diskList[$num]['Size']        = $disk->Size;
            $diskList[$num]['VolumeName']  = \owo\str2UTF8($disk->VolumeName);
        }
        return $this->data['logicalDisk'] = $diskList;
    }

    /**
     * 获取硬盘分区信息
     *
     * @param  array $exceptionCallParam
     * @return array
     */
    public function getDiskInfo(array $exceptionCallParam = [], &$call = []) : array
    {
        $diskInfo    = $this->getLogicalDiskRawInfo();
        $template    = '[Disk%d][%s] %s (%s) %d GB/ %d GB (%s)';
        $denominator = pow(1024, 3);
        $num         = count($diskInfo);
        $result      = [];
        for($i = 0; $i <= $num - 1; $i++)
        {
            $usedSpace   = $diskInfo[$i]['Size'] - $diskInfo[$i]['FreeSpace'];
            $usedPercent = round($usedSpace / $diskInfo[$i]['Size'] * 100, 2);
            $usedSpace   = $usedSpace / $denominator;
            $totalSize   = $diskInfo[$i]['Size'] / $denominator;
            $result[]    = sprintf($template, $i, $diskInfo[$i]['Caption'], $diskInfo[$i]['VolumeName'], $diskInfo[$i]['Description'], $usedSpace, $totalSize, $usedPercent . '%');
            if(!empty($exceptionCallParam)) {
                foreach($exceptionCallParam as $param) {
                    if(isset(${$param})) {
                        $call[$i][$param] = ${$param};
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 获取系统基本信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getServerRawInfo(bool $update = false) : array
    {
        $wmi      = $this->proxy_get('server', $update);
        $infoList = [];
        foreach($wmi as $info) {
            $infoList['LastBootUpTime']         = $info->LastBootUpTime;
            $infoList['TotalVisibleMemorySize'] = $info->TotalVisibleMemorySize;
            $infoList['FreePhysicalMemory']     = $info->FreePhysicalMemory;
            $infoList['Caption']                = \owo\str2UTF8($info->Caption);
            $infoList['CSDVersion']             = $info->CSDVersion;
            $infoList['SerialNumber']           = $info->SerialNumber;
            $infoList['InstallDate']            = $info->InstallDate;
        }
        return $this->data['server'] = $infoList;
    }

    /**
     * 获取格式化后的系统基本信息
     *
     * @param  boolean $update
     * @return array
     */
    public function getServerInfo(bool $update = false) : array
    {
        // 系统基本信息
        $serverInfo  = $this->getServerRawInfo($update);
        $runningTime = time() - strtotime(substr($serverInfo['LastBootUpTime'], 0, 14));
        $secPerDay   = 24 * 3600;
        $days        = floor($runningTime / $secPerDay);
        $hours       = floor(($runningTime - $days) / 3600);
        $minutes     = floor(($runningTime - ($days * $secPerDay) - ($hours * 3600)) / 60);
        $seconds     = floor($runningTime - ($days * $secPerDay) - ($hours * 3600) - ($minutes * 60) - 2);

        // 格式化输出转换
        $format = function($_) {
            return ($_ < 10) ? ('0' . $_) : $_;
        };
        $seconds     = ($seconds < 0) ? (60 + $seconds) : $seconds;
        $days        = $format($days);
        $hours       = $format($hours);
        $minutes     = $format($minutes);
        $seconds     = $format($seconds);

        // 内存使用信息
        $format = function($_, int $to = 2) {
            return round($_, $to);
        };
        $totalMemory       = $format($serverInfo['TotalVisibleMemorySize'] / 1024);
        $freeMemory        = $format($serverInfo['FreePhysicalMemory'] / 1024);
        $usedMemory        = $totalMemory - $freeMemory;

        // 格式化输出
        return [
            'runningTime'       => "{$days}天{$hours}时{$minutes}分{$seconds}秒",
            'memoryUsageInfo'   => "总内存: {$totalMemory} MB | 已使用: {$usedMemory} MB | 剩余: {$freeMemory} MB",
            'usedMemoryPercent' => $format($usedMemory / $totalMemory * 100),
            'restMemoryPercent' => $format($freeMemory / $totalMemory * 100)
        ];
    }
}
?>