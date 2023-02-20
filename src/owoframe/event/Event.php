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
 * @Date         : 2023-02-03 23:51:38
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-04 00:16:31
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\event;



abstract class Event
{
    /**
     * 事件名称
     *
     * @var string|null
     */
    protected $eventName = null;


    /**
     * 返回事件名称
     *
     * @return string
     */
    final public function getName() : string
    {
        return $this->eventName ?? get_class($this);
    }

    /**
     * 触发事件
     *
     * @return void
     */
    public function trigger() : void
    {
        $handlerList = EventManager::getInstance()->getHandlerList($this);
        foreach($handlerList as $callbacks) {
            foreach($callbacks as $callback) {
                $callback($this);
            }
        }
    }
}
?>