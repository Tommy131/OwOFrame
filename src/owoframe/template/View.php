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
 * @Date         : 2023-02-05 23:08:56
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-18 06:26:25
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\template;



class View extends Template
{
    /**
     * 显示控制区域变量绑定前缀
     */
    public const DISPLAY_CONTROL_PREFIX = 'display_id_';

    /**
     * HTML标签控制前缀
     */
    public const HTML_TAG_CONTROL_PREFIX = 'html_tag_control_';

    /**
     * 视图子级模板
     *
     * @access protected
     * @var string
     */
    protected $childTemplate = null;

    /**
     * 自定义资源路径
     *
     * @access protected
     * @var array
     */
    protected $customPath = [];


    /**
     * 绑定自定义资源路径
     *
     * @param  mixed $tag
     * @return void
     */
    public function bindCustomPath($tag) : void
    {
        if(is_array($tag)) {
            $this->customPath = array_merge($this->customPath, $tag);
        } else {
            $arg = func_get_args()[1];
            if(is_string($tag) && isset($arg) && is_string($arg)) {
                $this->customPath[$tag] = $arg;
            }
        }
    }

    /**
     * 删除一个自定义资源路径
     *
     * @param  string $tag
     * @return void
     */
    public function deleteCustomPath(string $tag) : void
    {
        if(isset($this->customPath[$tag])) {
            unset($this->customPath[$tag]);
        }
    }

    /**
     * 绑定一个Display控制ID
     *
     * @param  string  $cid Display 区域显示的控制ID
     * @param  boolean $status      显示状态
     * @return View
     */
    public function assignDisplayZone(string $cid, bool $status) : View
    {
        return $this->assign(self::DISPLAY_CONTROL_PREFIX . $cid, $status);
    }

    /**
     * 移除一个Display控制ID
     *
     * @param  string $cid Display 区域显示的控制ID
     * @return View
     */
    public function unassignDisplayZone(string $cid) : View
    {
        $cid = self::DISPLAY_CONTROL_PREFIX . $cid;
        if(isset($this->assigned[$cid])) {
            unset($this->assigned[$cid]);
        }
        return $this;
    }

    /**
     * 获取一个Display控制ID的状态
     *
     * @param  string $cid Display 区域显示的控制ID
     * @return boolean|null
     */
    public function getDisplayZoneStatus(string $cid) : ?bool
    {
        $cid = self::DISPLAY_CONTROL_PREFIX . $cid;
        return $this->{$cid} ?? null;
    }

    /**
     * 绑定一个HTML标签控制 (HTC)
     *
     * @param  string  $tag
     * @param  boolean $status 显示状态
     * @return View
     */
    public function assignHTC(string $tag, bool $status) : View
    {
        return $this->assign(self::HTML_TAG_CONTROL_PREFIX . $tag, $status);
    }

    /**
     * 移除一个HTML标签控制
     *
     * @param  string $tag
     * @return View
     */
    public function unassignHTC(string $tag) : View
    {
        $tag = self::HTML_TAG_CONTROL_PREFIX . $tag;
        if(isset($this->assigned[$tag])) {
            unset($this->assigned[$tag]);
        }
        return $this;
    }

    /**
     * 获取一个HTML标签控制
     *
     * @param  string $tag
     * @return boolean|null
     */
    public function getHTCStatus(string $tag) : ?bool
    {
        $tag = self::HTML_TAG_CONTROL_PREFIX . $tag;
        return $this->{$tag} ?? null;
    }

    /**
     * 设置父级模板, 当前加载的模板将会替换为嵌套模板
     *
     * ~Usage:     模板文件需要配合使用 <owo type="childTemplate" /> 以进行模板更改
     * *Attention: 使用者需要在合适的位置及场景添加上述标签
     *
     * @param  string  $filePath
     * @param  boolean $update
     * @return View
     */
    public function setParentTemplate(string $filePath, bool $update = false) : View
    {
        if(!$this->hasChildTemplate() || $update) {
            if(is_null($this->viewTemplate)) {
                $this->load(true);
            }
            $this->childTemplate = $this->viewTemplate;
            $this->viewTemplate  = Path::getFile(Path::view($filePath));
        }
        return $this;
    }

    /**
     * 设置子级模板
     *
     * @param  string  $filePath
     * @param  boolean $update
     * @return View
     */
    public function setChildTemplate(string $filePath, bool $update = false) : View
    {
        if(!$this->hasChildTemplate() || $update) {
            $this->childTemplate = Path::getFile(Path::view($filePath));
        }
        return $this;
    }

    /**
     * 判断是否存在子级模板
     *
     * @return boolean
     */
    public function hasChildTemplate() : bool
    {
        return !is_null($this->childTemplate);
    }

    /**
     * 解析前端模板存在的区域控制显示语法
     *
     * ~ Usage 1:  <owo-v-display default="true" @cid="controlId">HTML-TAGS</owo-v-display>
     * ~ Usage 2:  <owo-v-display default="false" @cid="controlId">HTML-TAGS</owo-v-display>
     * ~ Usage 3:  <owo-v-display @cid="controlId">HTML-TAGS</owo-v-display>
     *
     * *Attention: 第3种情况缺省 `default="(status: boolean)"` 则默认为不显示
     *
     * @param  string $str 需要解析的文本
     * @return void
     */
    protected function parseDisplayArea(string &$str) : void
    {
        if(preg_match_all('/<owo-v-display ?(default="(true|false)")? @cid="(\w+)">(.*)<\/owo-v-display>/imsuU', $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[0] as $k => $area) {
                // 区域ID绑定解析;
                $cid = $matches[3][$k];
                // 区域display默认状态 (布尔值);
                $display = strtolower($matches[2][$k]);
                $display = ($display === 'true') ? true : false;
                if(strlen($cid) > 0) {
                    if(is_bool($value = $this->getDisplayZoneStatus($cid))) {
                        $display = $value;
                    }
                }
                // 区域原文;
                $original = $matches[4][$k];
                // 最终判断;
                $strings[] = ($display) ? $area : \owo\get_new_line($area, $str);
                $replace[] = ($display) ? $original : '';
            }
            $str = str_replace($strings, $replace, $str);
        }
    }

    /**
     * 解析 OwO语句
     *
     * ~ Usage: <owo type="component" src="资源路径" />
     * ~ Usage: see View->setParentTemplate($filePath: string, $update: boolean)
     *
     * @param  string $str
     * @return void
     */
    protected function parseOwOSentence(string &$str) : void
    {
        $regex = '/<owo type="(\w+)" ?(src="(.*)")?[\s ]?+\/>/imU';
        $strings = $replace = [];
        while(preg_match_all($regex, $str, $matches)) {
            foreach($matches[1] as $k => $type) {
                switch($type) {
                    case 'component':
                        $strings[] = $matches[0][$k];
                        $path      = Path::component($matches[3][$k]);
                        $replace[] = Path::getFile($path);
                    break;

                    case 'childComponent':
                        $strings[] = $matches[0][$k];
                        $replace[] = $this->childTemplate;
                    break;

                    default:
                        $strings[] = \owo\get_new_line($matches[0][$k], $str);
                        $replace[] = '';
                    break;
                }
            }
            $str = str_replace($strings, $replace, $str);
        }
    }

    /**
     * 渲染视图到前端
     *
     * @param bool $update
     * @return string
     */
    public function render(bool $update = false) : string
    {
        if($this->isRendered() && !$update) {
            return $this->viewTemplate;
        }
        if(is_null($this->viewTemplate)) {
            $this->load(true);
        }

        // 第一次调用, 防止使用者在OwO模板语句中写入变量
        $this->replaceAllAssigned($this->viewTemplate);
        // 解析OwO语句
        $this->parseOwOSentence($this->viewTemplate);
        // 转换常量绑定
        $this->replaceConstants($this->viewTemplate);
        // 解析绑定数组
        $this->replaceArray($this->viewTemplate);
        // 解析循环语句
        $level = \owo\_global('view.loopLevel', 3);
        \owo\str($level);
        for($i = null; $i <= $level; $i++) {
            $this->parseLoopArea($this->viewTemplate, $i);
        }
        // 绑定变量
        $this->replaceAllAssigned($this->viewTemplate);
        // 解析@display语法
        $this->parseDisplayArea($this->viewTemplate);
        // 解析IF-ELSE语法区域
        $level = \owo\_global('view.judgementLevel', 3);
        \owo\str($level);
        for($i = null; $i <= $level; $i++) {
            $this->parseJudgementArea($this->viewTemplate, $i);
        }
        // 解析模板语法之函数调用
        $this->replaceFunction($this->viewTemplate);
        // 绑定资源路径到路由
        Path::parseResourcePath($this->assigned, $this->customPath, $this->viewTemplate);

        $this->isRendered = true;
        return $this->viewTemplate;
    }
}
?>