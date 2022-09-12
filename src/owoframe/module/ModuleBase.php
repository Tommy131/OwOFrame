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
    /**
     * 插件加载路径
     *
     * @access private
     * @var string
     */
    private $loadPath;

    /**
     * 插件信息配置文件(JSON对象传入) | Plugin Information Configuration (Json Format Object)
     *
     * @access private
     * @var object
     */
    private $moduleInfo;

    /**
     * 插件已加载值
     *
     * @access protected
     * @var boolean
     */
    protected $isEnabled = false;



    /**
     * 实例化插件时的构造函数
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @param  string      $loadPath   插件加载路径
     * @param  object      $moduleInfo 插件信息配置文件
     * @return void
     */
    final public function __construct(string $loadPath, object $moduleInfo)
    {
        $this->loadPath   = $loadPath;
        $this->moduleInfo = $moduleInfo;
    }


    /**
     * 插件加载时自动调用此方法: 正确加载时
     *
     * @return void
     */
    public function onLoad() : void
    {}

    /**
     * 插件加载时自动调用此方法: 正确启动时
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @return void
     */
    abstract public function onEnable() : void;

    /**
     * 插件卸载时的处理方法
     *
     * @return void
     */
    public function onDisable() : void
    {}

    /**
     * 获取插件信息对象
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @return object
     */
    final public function getInfos() : object
    {
        return $this->moduleInfo;
    }

    /**
     * 获取插件加载路径
     *
     * @author HanskiJay
     * @since  2021-01-23
     * @return string
     */
    final public function getPath() : string
    {
        return $this->loadPath;
    }

    /**
     * 返回插件加载状态
     *
     * @author HanskiJay
     * @since  2021-03-02
     * @return boolean
     */
    public function isEnabled() : bool
    {
        return $this->isEnabled;
    }

    /**
     * 设置插件加载状态为已加载
     *
     * @author HanskiJay
     * @since  2021-03-02
     * @return void
     */
    public function setEnabled() : void
    {
        if(!$this->isEnabled()) {
            $this->isEnabled = true;
        }
    }

    /**
     * 设置插件加载状态为禁用
     *
     * @author HanskiJay
     * @since  2021-03-02
     * @return void
     */
    public function setDisabled() : void
    {
        if($this->isEnabled()) {
            $this->isEnabled = false;
        }
    }
}