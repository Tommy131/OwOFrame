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
 * @Date         : 2023-02-02 18:41:51
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-02 18:41:51
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\object;



class JSON extends Config
{
    /**
     * 保存配置文件
     *
     * @param  string|null $file
     * @return void
     */
    public function save(?string $file = null) : void
    {
        if($file !== null) {
            $this->__construct($file, $this->config, $this->autoSave);
        }
        file_put_contents($file ?? $this->getFullName(), json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * 重新读取配置文件
     *
     * @return boolean
     */
    protected function reloadCallback() : bool
    {
        $this->nestedCache = [];
        $this->config = json_decode(file_get_contents($this->getFullName()), true) ?? [];
        return true;
    }

    /**
     * 返回配置文件扩展名称
     *
     * @return string
     */
    public function getExtensionName() : string
    {
        return '.json';
    }
}
?>