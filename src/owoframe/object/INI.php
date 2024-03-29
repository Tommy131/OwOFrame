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
 * @Date         : 2023-02-02 18:52:12
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-02 18:59:05
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\object;



class INI extends Config
{
    /**
     * 从配置文件加载到全局
     *
     * @param  string  $file
     * @param  array   $defaultData
     * @param  boolean $autoSave
     * @return void
     */
    public static function loadFile2Global(string $file, array $defaultData = [], bool $autoSave = false) : void
    {
        global $_global;
        $_global = new static($file, $defaultData, $autoSave);
    }

    /**
     * 从配置文件实例加载到全局
     *
     * @param  INI $object
     * @return void
     */
    public static function toGlobal(INI $object) : void
    {
        global $_global;
        $_global = $object;
    }

    /**
     * 读取全局配置文件 | get global configuration
     *
     * @param  string $index
     * @param  mixed  $default
     * @return mixed
     */
    public static function _global(string $index, $default = null)
    {
        global $_global;
        return ($_global instanceof INI) ? $_global->get($index, $default) : $default;
    }

    /**
     * 保存配置文件
     *
     * @param  string|null $file
     * @return void
     */
    public function save(?string $file = null) : void
    {
        if(empty($this->config)) {
            return;
        }

        $parseDataType = function($value) {
            if(is_null($value) || (is_string($value) && (strlen($value) === 0))) {
                $value = 'null';
            }
            elseif(($value === false)) {
                $value = 'false';
            }
            elseif(($value === true)) {
                $value = 'true';
            }
            return $value;
        };
        $text = '';

        foreach($this->config as $group => $subContent) {
            $text .= "[{$group}]" . PHP_EOL;
            foreach($subContent as $name => $value) {
                if(!is_array($value)) {
                    $value = $parseDataType($value);
                    $text .= "{$name}={$value}" . PHP_EOL;
                } else {
                    $value = $name . '=' . implode(',', $value);
                }
            }
            $text .= PHP_EOL;
        }
        file_put_contents($file ?? $this->getFullName(), trim($text));
    }

    /**
     * 重新读取配置文件
     *
     * @return boolean
     */
    protected function reloadCallback() : bool
    {
        $this->config = parse_ini_file($this->getFullName(), true);
        return true;
    }

    /**
     * 返回配置文件扩展名称
     *
     * @return string
     */
    public function getExtensionName() : string
    {
        return '.ini';
    }

    /**
     * 解析配置文件数据
     * Base64 encoded:
     * CXB1YmxpYyBmdW5jdGlvbiBwYXJzZVJhd0RhdGEoKSA6IHZvaWQNCgl7DQoJCSR0aGlzLSZndDtjb25maWcgPSBwYXJzZV9pbmlfZm
     * lsZSgkdGhpcy0mZ3Q7ZmlsZVBhdGggLiAkdGhpcy0mZ3Q7ZmlsZU5hbWUsIHRydWUpOw0KCQlmb3JlYWNoKCR0aGlzLSZndDtjb25m
     * aWcgYXMgJGdyb3VwID0mZ3Q7ICRzdWJjb250ZW50KSB7DQoJCQlmb3JlYWNoKCRzdWJjb250ZW50IGFzICRuYW1lID0mZ3Q7ICR2YW
     * x1ZSkgew0KCQkJCSRhcnIgPSBhcnJheV9maWx0ZXIoZXhwbG9kZSgmIzM5Oy4mIzM5OywgJG5hbWUpKTsNCgkJCQlpZihjb3VudCgk
     * YXJyKSAmbHQ7PSAxKSBjb250aW51ZTsNCgkJCQkkY3VycmVudCA9JiAkdGhpcy0mZ3Q7Y29uZmlnWyRncm91cF07DQoJCQkJZm9yZW
     * FjaCgkYXJyIGFzICRrZXkpIHsNCgkJCQkJaWYoIWlzc2V0KCRjdXJyZW50WyRrZXldKSkgew0KCQkJCQkJJGN1cnJlbnRbJGtleV0g
     * PSBbXTsNCgkJCQkJfQ0KCQkJCQkkY3VycmVudCA9JiAkY3VycmVudFska2V5XTsNCgkJCQl9DQoJCQkJJGN1cnJlbnQgPSAkdmFsdWU
     * 7DQoJCQkJdW5zZXQoJHRoaXMtJmd0O2NvbmZpZ1skZ3JvdXBdWyRuYW1lXSk7DQoJCQl9DQoJCX0NCgl9
     */
}
?>