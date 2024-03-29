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
 * @Date         : 2023-02-02 17:33:55
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-23 07:14:37
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\object;



abstract class Config
{
    /**
     * 配置文件数组
     *
     * @access protected
     * @var array
     */
    protected $config;

    /**
     * 配置文件嵌套缓存数组
     *
     * @access protected
     * @var array
     */
    protected $nestedCache;

    /**
     * 文件名
     *
     * @access protected
     * @var string
     */
    protected $fileName;

    /**
     * 文件路径
     *
     * @access protected
     * @var string
     */
    protected $filePath;

    /**
     * 自动保存
     *
     * @var bool
     */
    public $autoSave;


    public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
    {
        $this->autoSave = $autoSave;
        $this->filePath = dirname($file) . DIRECTORY_SEPARATOR;
        if(!is_dir($this->filePath)) {
            mkdir($this->filePath, 755, true);
        }
        $fileName       = explode('.', basename($file)); // e.g. abc.json | abc
        $fileName       = array_shift($fileName) ?? '';  // if yes, then shift 'abc' to $file
        $this->fileName = str_replace($this->filePath, '', $fileName);


        if(!file_exists($file) || (filesize($file) === 0)) {
            $this->config = $defaultData;
            $this->save();
        } else {
            $this->reload();
        }
    }

    /**
     * 获取配置文件项目
     *
     * @param  string $index   键值
     * @param  mixed  $default 默认返回值
     * @return mixed
     */
    public function get(string $index, $default = null)
    {
        $arr = explode('.', $index);
        if(count($arr) > 1) {
            if(isset($this->nestedCache[$index])) return $this->nestedCache[$index];

            $base = array_shift($arr);
            if(isset($this->config[$base])) {
                $base = $this->config[$base];
            } else {
                return $default;
            }

            while(count($arr) > 0) {
                $baseKey = array_shift($arr);
                if(is_array($base) && isset($base[$baseKey])) {
                    $base = $base[$baseKey];
                } else {
                    return $default;
                }
            }
            return $this->nestedCache[$index] = $base;
        } else {
            return $this->config[$index] ?? $default;
        }
    }

    /**
     * 向对象设置属性
     *
     * @param  string $index 键值
     * @param  mixed  $value 数据
     * @return Config
     */
    public function set(string $index, $value) : Config
    {
        $arr = explode('.', $index);
        if(count($arr) > 1) {
            $base = array_shift($arr);
            if(!isset($this->config[$base])) {
                $this->config[$base] = [];
            }

            $base =& $this->config[$base];
            if(!is_array($base)) {
                return $this;
            }

            while(count($arr) > 0) {
                $baseKey = array_shift($arr);
                if(!isset($base[$baseKey])) {
                    $base[$baseKey] = [];
                }
                $base =& $base[$baseKey];
            }
            $base = $value;
            $this->nestedCache[$index] = $value;
        } else {
            $this->config[$index] = $value;
        }
        if($this->autoSave) $this->save();
        return $this;
    }

    /**
     * 向对象设置属性
     *
     * @param  array $data 数据
     * @return Config
     */
    public function setAll(array $data) : Config
    {
        $this->config = $data;
        if($this->autoSave) $this->save();
        return $this;
    }

    /**
     * 移除变量值
     *
     * @param  string $index 键名
     * @return Config
     */
    public function remove(string $index) : Config
    {
        if($this->exists($index)) {
            unset($this->config[$index]);
            if($this->autoSave) $this->save();
        }
        return $this;
    }

    /**
     * 保存配置文件
     *
     * @param  string|null $file 文件
     * @return void
     */
    abstract public function save(?string $file = null) : void;

    /**
     * 重新读取配置文件
     *
     * @return bool
     */
    abstract protected function reloadCallback() : bool;

    /**
     * 重新读取配置文件
     *
     * @return bool
     */
    public function reload() : bool
    {
        if(is_file($this->getFullName())) {
            return $this->reloadCallback();
        }
        return false;
    }

    /**
     * 备份配置文件
     *
     * @param  string $backupPath 备份路径
     * @return void
     */
    public function backup(string $backupPath = '') : void
    {
        if(!is_dir($backupPath)) {
            $backupPath = $this->getFilePath();
        }
        $this->save($backupPath . $this->getFileName() . '_' . date('Ymd_His') . $this->getExtensionName());
    }

    /**
     * 判断键值是否存在
     *
     * @param  string $index 键值
     * @return boolean
     */
    public function exists(string $index) : bool
    {
        return isset($this->config[$index]);
    }

    /**
     * 返回配置文件
     *
     * @return array
     */
    public function getAll() : array
    {
        return (array) $this->config;
    }

    /**
     * 返回数组长度
     *
     * @return integer
     */
    public function length() : int
    {
        return count($this->config);
    }

    /**
     * 将当前的数据转换成对象 | Formatting currently data($this->config) to Object
     *
     * @return object
     */
    public function obj() : object
    {
        return (object) $this->config;
    }

    /**
     * 返回当前配置文件名称
     *
     * @return string
     */
    public function getFileName() : string
    {
        return $this->fileName;
    }

    /**
     * 返回当前配置文件路径
     *
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->filePath;
    }

    /**
     * 返回配置文件完整路径
     *
     * @return string
     */
    public function getFullName() : string
    {
        return $this->filePath . $this->fileName . $this->getExtensionName();
    }

    /**
     * 返回配置文件扩展名称
     *
     * @return string
     */
    abstract public function getExtensionName() : string;
}
?>