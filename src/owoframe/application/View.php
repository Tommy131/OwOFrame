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
namespace owoframe\application;

use owoframe\exception\OwOFrameException;
use owoframe\exception\ParameterTypeErrorException;
use owoframe\exception\ResourceNotFoundException;
use owoframe\utils\Str;

class View
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
     * 渲染状态
     *
     * @access private
     * @var boolean
     */
    private $isRendered = false;

    /**
     * 视图模板名称
     *
     * @access protected
     * @var string
     */
    protected $templateName;

    /**
     * 视图文件路径
     *
     * @access protected
     * @var string
     */
    protected $filePath = null;

    /**
     * 视图模板
     *
     * @access protected
     * @var string
     */
    protected $viewTemplate = null;

    /**
     * 视图子级模板
     *
     * @access protected
     * @var string
     */
    protected $childTemplate = null;

    /**
     * 模板绑定的变量
     *
     * @access protected
     * @var array
     */
    protected $bindValues = [];

    /**
     * 绑定常量到模板
     *
     * @access protected
     * @var array
     */
    protected $constants = [];

    /**
     * 自定义资源路径
     *
     * @access protected
     * @var array
     */
    protected $customPath = [];

    /**
     * 模板扩展名称
     *
     * @var string
     */
    public static $extensionName;


    /**
     * 初始化OwO视图引擎
     *
     * @author HanskiJay
     * @since  2022-08-01
     * @param  string $templateName 视图模板名称
     * @param  string $filePath     文件路径
     */
    public function __construct(string $templateName = '', string $filePath = '')
    {
        // 智能获取模板文件名;
        if($templateName === '') {
            $lastCallerData = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            array_shift($lastCallerData);
            $lastCallerData = array_shift($lastCallerData);
            $templateName   = $lastCallerData['function'];
            if($templateName === '__construct') {
                $templateName = explode('\\', $lastCallerData['class']);
                $templateName = end($templateName);
            }
            $templateName = ucfirst(strtolower($templateName));
        } else {
            $templateName = explode(DIRECTORY_SEPARATOR, $templateName);
            $templateName = explode('.', array_shift($templateName));
            $templateName = array_shift($templateName);
        }
        $this->templateName = $templateName;

        // 判断文件路径是否有效;
        if($filePath === '') {
            $filePath = Path::getViewPath($templateName . self::getExtensionName(), $isExists);
            if(!$isExists) {
                throw new ResourceNotFoundException('Template', $filePath);
            }
        } else {
            Path::filterPath($filePath);
            $filePath = $filePath . DIRECTORY_SEPARATOR . $templateName . self::getExtensionName();
            if(!file_exists($filePath)) {
                throw new ResourceNotFoundException('Template', $filePath);
            }
        }
        $this->filePath = $filePath;
    }

    /**
     * 初始化获取模板
     *
     * @author HanskiJay
     * @since  2022-08-02
     * @param  boolean $update
     * @return View
     */
    public function init(bool $update = false) : View
    {
        if($update || is_null($this->viewTemplate)) {
            $this->viewTemplate = file_get_contents($this->filePath);
        }
        return $this;
    }


    /**
     * 合并常量定义
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @return View
     */
    public function mergeConstants(array $arr) : View
    {
        $this->constants = array_merge($this->constants, $arr);
        return $this;
    }

    /**
     * 绑定变量到模板
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string|array $index 需要替换的变量名
     * @param  mixed|null   $val   替换的值
     * @return View
     */
    public function assign($index, $val = null) : View
    {
        if(is_array($index)) {
            $this->bindValues = array_merge($this->bindValues, $index);
        }
        elseif(is_string($index)) {
            if(!is_null($val)) {
                $this->bindValues[$index] = $val;
            }
        }

        return $this;
    }

    /**
     * 解除绑定模板中的变量定义
     *
     * @author HanskiJay
     * @since  2021-04-26
     * @param  string $index 需要解除的变量名
     * @return View
     */
    public function unassign(string $index) : View
    {
        if(isset($this->bindValues[$index])) {
            unset($this->bindValues[$index]);
        }
        return $this;
    }

    /**
     * 获取绑定标签的值
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string $index   需要查找的键名
     * @param  string $default 默认返回值
     * @return mixed
     */
    public function getAssign(string $index, $default = null)
    {
        return $this->bindValues[$index] ?? $default;
    }

    /**
     * 绑定自定义资源路径
     *
     * @author HanskiJay
     * @since  2021-12-24
     * @param  [type] $mixed
     * @return void
     */
    public function bindCustomPath($mixed) : void
    {
        if(is_array($mixed)) {
            $this->customPath = array_merge($this->customPath, $mixed);
        } else {
            $arg = func_get_args()[1];
            if(is_string($mixed) && isset($arg) && is_string($arg)) {
                $this->customPath[$mixed] = $arg;
            }
        }
    }

    /**
     * 删除一个自定义资源路径
     *
     * @author HanskiJay
     * @since  2021-12-24
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
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string  $cid Display区域显示的控制ID
     * @param  boolean $status 显示状态
     * @return View
     */
    public function assignDisplayZone(string $cid, bool $status) : View
    {
        return $this->assign(self::DISPLAY_CONTROL_PREFIX . $cid, $status);
    }

    /**
     * 移除一个Display控制ID
     *
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string $cid Display区域显示的控制ID
     * @return View
     */
    public function unassignDisplayZone(string $cid) : View
    {
        $cid = self::DISPLAY_CONTROL_PREFIX . $cid;
        if(isset($this->bindValues[$cid])) {
            unset($this->bindValues[$cid]);
        }
        return $this;
    }

    /**
     * 获取一个Display控制ID的状态
     *
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string $cid Display区域显示的控制ID
     * @return boolean|null
     */
    public function getDisplayZoneStatus(string $cid) : ?bool
    {
        return $this->getAssign(self::DISPLAY_CONTROL_PREFIX . $cid);
    }



    /**
     * 绑定一个HTML标签控制 (HTC)
     *
     * @author HanskiJay
     * @since  2021-12-21
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
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string $tag
     * @return View
     */
    public function unassignHTC(string $tag) : View
    {
        $tag = self::HTML_TAG_CONTROL_PREFIX . $tag;
        if(isset($this->bindValues[$tag])) {
            unset($this->bindValues[$tag]);
        }
        return $this;
    }

    /**
     * 获取一个HTML标签控制
     *
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string $tag
     * @return boolean|null
     */
    public function getHTCStatus(string $tag) : ?bool
    {
        return $this->getAssign(self::HTML_TAG_CONTROL_PREFIX . $tag);
    }

    /**
     * 设置父级模板, 当前加载的模板将会替换为嵌套模板
     *
     * ~Usage:     模板文件需要配合使用 <owo type="childTemplate" /> 以进行模板更改
     * *Attention: 使用者需要在合适的位置及场景添加上述标签
     *
     * @author HanskiJay
     * @since  2022-08-03
     * @param  string  $filePath
     * @param  boolean $update
     * @return View
     */
    public function setParentTemplate(string $filePath, bool $update = false) : View
    {
        if(!$this->hasChildTemplate() || $update) {
            if(is_null($this->viewTemplate)) {
                $this->init(true);
            }
            $this->childTemplate = $this->viewTemplate;
            $this->viewTemplate  = Path::getFile(Path::getViewPath($filePath));
        }
        return $this;
    }

    /**
     * 设置子级模板
     *
     * @author HanskiJay
     * @since  2022-08-03
     * @param  string  $filePath
     * @param  boolean $update
     * @return View
     */
    public function setChildTemplate(string $filePath, bool $update = false) : View
    {
        if(!$this->hasChildTemplate() || $update) {
            $this->childTemplate = Path::getFile(Path::getViewPath($filePath));
        }
        return $this;
    }

    /**
     * 判断是否存在子级模板
     *
     * @author HanskiJay
     * @since  2022-08-03
     * @return boolean
     */
    public function hasChildTemplate() : bool
    {
        return !is_null($this->childTemplate);
    }


    #-----------------------------------------------------------------------------------#
    /**
     * OwOView引擎核心解析区域
     *
     * @author HanskiJay
     */
    #-----------------------------------------------------------------------------------#
    /**
     * 替换绑定变量
     *
     * ~Usage: {$bindValue}
     * ~Usage: {$bindValue|defaultOutput}
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @param  string $key   变量名
     * @param  mixed  $value 变量值
     * @param  string &$str  原始字符串
     * @return View
     */
    protected function replaceBindValue(string $key, $value, string &$str) : View
    {
        if(preg_match('/{\$' . $key . '\|(.*)}/muU', $str, $match)) {
            if(is_bool($value)) {
                changeBool2String($value);
            }
            $str = str_replace($match[0], $value ?? (($match[1] === ':null') ? '' : $match[1]), $str);
        }
        $str = str_replace("{\${$key}}", $value, $str);
        return $this;
    }

    /**
     * 替换绑定变量
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @param  string &$str 原始字符串
     * @return View
     */
    protected function replaceBindValues(string &$str) : View
    {
        if(preg_match_all('/{\$(\w+)(\|(.*))?}/muU', $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[1] as $k => $bindTag) {
                $strings[] = $matches[0][$k];
                // 从绑定变量数组中获取绑定值;
                if(!is_null($result = $this->getAssign($bindTag))) {
                    $replace[] = $result;
                } else {
                    // 判断是否存在默认值;
                    $replace[] = isset($matches[3][$k]) ? (($matches[3][$k] === ':null') ? '' : $matches[3][$k]) : $matches[0][$k];
                }
            }
            $str = str_replace($strings, $replace, $str);
        }
        return $this;
    }

    /**
     * 替换绑定数组
     *
     * !ATTENTION: 目前仅支持一维数组!!!
     * @author HanskiJay
     * @since  2021-12-25
     * @param  string $str
     * @return View
     */
    protected function replaceBindArrays(string &$str) : View
    {
        if(preg_match_all('/{\$(\w+)\.(\w+)(\|(.*))?}/muU', $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[1] as $k => $bindTag) {
                $strings[] = $matches[0][$k];
                // 从绑定变量数组中获取绑定值;
                if(!is_null($result = $this->getAssign($bindTag))) {
                    $key = $matches[2][$k];
                    if(isset($result[$key])) {
                        $replace[] = $result[$key];
                    } else {
                        // 判断是否存在默认值;
                        $replace[] = (isset($matches[4][$k])) ? (($matches[4][$k] === ':null') ? '' : $matches[4][$k]) : $matches[0][$k];
                    }
                } else {
                    $replace[] = '';
                }
            }
            $str = str_replace($strings, $replace, $str);
        }
        return $this;
    }

    /**
     * 解析前端模板存在的循环语法
     *
     * ~usage  {loop @bindLoopTag in $bindTag_from_assign}HTML-TAGS{/loop}
     * ~调用   @bindLoopTag.arrayElementKey@
     *
     * @author HanskiJay
     * @since  2021-01-03
     * @param  string       $loopArea 需要解析的文本
     * @param  integer|null $level    循环次数
     * @return void
     */
    protected function parseLoopArea(string &$loopArea, ?int $level = null) : void
    {
        // $bindElement = "\\\$";
        $bindElement = '@';
        $loopHead    = "{{$level}loop {$bindElement}([a-zA-Z0-9]+) in \\\$([a-zA-Z0-9]+)}";
        $loopBetween = '([\s\S]*)';
        $loopEnd     = "{\/{$level}loop}";
        $loopRegex   = "/{$loopHead}{$loopBetween}{$loopEnd}/muU";

        if(!preg_match_all($loopRegex, $loopArea, $matched, PREG_SET_ORDER, 0)) {
            return;
        }
        $currentKey = 1;
        foreach($matched as $num => $loopGroup) {
            $bindTag  = trim($loopGroup[2]);                // 绑定的数组变量到模板;
            $defined  = trim($loopGroup[1]);                // 定义的变量到模板;
            // $loopArea = trim(preg_replace("/{$loopEnd}/im", '', preg_replace("/{$loopHead}/im", '', $loopArea)));
            // $loopArea = explode("\n", trim($loopArea));      // 匹配到的循环语句;
            $loop     = explode("\n", trim($loopGroup[0]));      // 匹配到的循环语句;

            $data = $this->getAssign($bindTag);
            if(!is_array($data)) {
                $loopArea = '';
                return;
                // throw new OwOFrameException("[View-LoopParserError] Cannot find bindTag \${$bindTag} !");
            }

            $data = array_filter($data);
            $complied = [];
            $finally   = '';
            foreach($data as $k => $v) {
                if(!is_array($v)) {
                    throw new ParameterTypeErrorException('不合法的使用方法!', 'array', $v);
                }

                foreach($loop as $n => $line) {
                    if(preg_match("/({$loopEnd}|{$loopHead})/im", $line)) {
                        continue;
                    }
                    $line   = trim($line);
                    $length = strlen($line);
                    if(preg_match_all("/{$defined}?([\\\.]?[a-zA-Z0-9]){0,$length}/", $line, $match)) {
                        $matchedTags = array_shift($match);    // 获取到的原始绑定标签集;
                        foreach($matchedTags as $matchedTag) {
                            $parseArray = explode('.', $matchedTag); // 解析并分级绑定标签;
                            array_shift($parseArray);                // 去除第一级原始绑定标签;

                            if((count($parseArray) === 0) && (($cnum = count($data)) > 1)) {
                                $complied[$k][$n] = str_replace($bindElement . $matchedTag . $bindElement, "Array(n:{$bindElement}{$bindTag})[{$cnum}]", $line);
                            } else {
                                $current = $v;
                                while($parseArray) {
                                    $next = array_shift($parseArray);
                                    if(is_array($current)) {
                                        if(isset($current[$next])) {
                                            $current = $current[$next];
                                        } else {
                                            $current = "Array(undefined:{$bindElement}{$bindTag}.{$next})";
                                        }
                                    }
                                }
                                $complied[$k][$n] = str_replace($bindElement . $matchedTag . $bindElement, $current, $complied[$k][$n] ?? $line);
                            }
                        }
                    } else {
                        $complied[$k][$n] = $line;
                    }
                    if(preg_match_all("/{$bindElement}currentKey{$bindElement}/", $line, $match)) {
                        $complied[$k][$n] = str_replace($bindElement . 'currentKey' . $bindElement, (string) $currentKey++, $complied[$k][$n] ?? $line);
                    }
                }
                ksort($complied[$k]);
                foreach($complied[$k] as $result) {
                    $finally .= $result . PHP_EOL;
                }
            }
            $loopArea = str_replace($matched[$num][0], $finally, $loopArea);
        }
    }

    /**
     * 判断语句拆分
     *
     * @author HanskiJay
     * @since  2021-12-25
     * @param  string  $type
     * @param  mixed  $condition1
     * @param  mixed  $condition2
     * @return boolean
     */
    protected static function checkJudgement(string $type, $condition1, $condition2) : bool
    {
        $result = false;
        switch(strtolower($type)) {
            case '==';
            case 'eq':
            case 'equal':
                $result = $condition1 == $condition2;
            break;

            case '===':
            case 'eqs':
            case 'equals':
                $result = $condition1 === $condition2;
            break;

            case '!=':
            case '!eq':
            case 'neq':
            case '!equal':
            case 'nequal':
                $result = $condition1 != $condition2;
            break;

            case '!==':
            case '!eqs':
            case 'neqs':
            case '!equals':
            case 'nequals':
                $result = $condition1 !== $condition2;
            break;

            case '>':
            case 'gt':
            case 'bigger':
            case 'big':
                $result = $condition1 > $condition2;
            break;

            case '>=':
                $result = $condition1 >= $condition2;
            break;

            case '<':
            case 'lt':
            case 'smaller':
            case 'small':
                $result = $condition1 < $condition2;
            break;

            case '<=':
                $result = $condition1 <= $condition2;
            break;
        }
        return $result;
    }

    /**
     * IF-ELSE 语句解析区域
     *
     * *ATTENTION: 这个语法支持简单判断, 判断顺序依次为从左往右!
     * ~Usage  {if condition1 Judgement condition2 and|&& condition3 Judgement condition4...}
     * @see    Tested picture in /tests/Function_[View-parseJudgementArea()]_test_log.png
     * @author HanskiJay
     * @since  2021-12-25
     * @param  string       $str   需要解析的文本
     * @param  integer|null $level 循环次数
     * @return void
     */
    protected function parseJudgementArea(string &$str, ?int $level = null) : void
    {
        $regex1 = "/{{$level}if (.*)}(.*){\/{$level}if}/msuU";
        $regex2 = "/{{$level}if (.*)}(.*){else}(.*){\/{$level}if}/msuU"; //优先匹配;
        while(preg_match_all($regex2, $str, $matches) || preg_match_all($regex1, $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[1] as $k => $v)
            {
                $strings[] = $matches[0][$k];
                $lastResult = null;
                $lastJudge  = null;
                while(strlen($v) > 0)
                {
                    if(preg_match('/([\$\w]+) ([\w!=<>]+) ([\$\w]+)/u', $v, $matched))
                    {
                        $currentSentence = $matched[0];
                        $v = trim(str_replace($currentSentence, '', $v));
                        // echo '[0] Current Judgement sentence: ' . $currentSentence . PHP_EOL;

                        if(strpos($matched[1], '$') !== false) {
                            $matched[1] = $this->getAssign(substr($matched[1], 1, strlen($matched[1])));
                        }
                        if(strpos($matched[3], '$') !== false) {
                            $matched[3] = $this->getAssign(substr($matched[3], 1, strlen($matched[3])));
                        }
                        if(is_string($matched[1])) {
                            changeType($matched[1], $matched[1]);
                        }
                        if(is_string($matched[3])) {
                            changeType($matched[3], $matched[3]);
                        }
                        $result = self::checkJudgement($matched[2], $matched[1], $matched[3]);
                        // changeBool2String($result, $r);
                        // echo '[1] Current Judgement result: ' . ($r) . PHP_EOL;

                        if(is_string($lastJudge)) {
                            // changeBool2String($lastResult, $r);
                            // echo '[2] Last result (not compared): ' . ($r) . PHP_EOL . PHP_EOL;
                            switch(strtolower($lastJudge)) {
                                case 'and':
                                case '&&':
                                    $lastResult = $lastResult && $result;
                                break;

                                case 'or':
                                case '||':
                                    $lastResult = $lastResult || $result;
                                break;
                            }
                            // changeBool2String($lastResult, $r);
                            // echo '[3] Compared result: ' . ($r) . PHP_EOL . PHP_EOL;
                        }

                        if(preg_match('/[\w\|&]+/', $v, $lastJudge)) {
                            $lastJudge = array_shift($lastJudge);
                            // echo '[4] Compare Judgement grammar: ' . $lastJudge . PHP_EOL;
                        }

                        if(is_string($lastJudge)) {
                            $v = substr($v, strlen($lastJudge), strlen($v));
                            // echo '[5] Next sentence (not Judgement): ' . $v . PHP_EOL . PHP_EOL;
                            if(strlen($v) > 0) {
                                $lastResult = $result;
                            }
                        }
                    } else {
                        throw new OwOFrameException('[ERROR] Grammar mistake: Invalid Judgement Sentence \'' . $v . '\'');
                        break;
                    }
                }
                $replace[] = ($lastResult ?? $result) ? $matches[2][$k] : trim($matches[3][$k] ?? '', "\r\n\t");
            }
            $str = str_replace($strings, $replace, $str);
        }
    }

    /**
     * 解析前端模板存在的区域控制显示语法
     *
     * ~Usage 1:  <owo-v-display default="true" @cid="controlId">HTML-TAGS</owo-v-display>
     * ~Usage 2:  <owo-v-display default="false" @cid="controlId">HTML-TAGS</owo-v-display>
     * ~Usage 3:  <owo-v-display @cid="controlId">HTML-TAGS</owo-v-display>
     *
     * *Attention: 第3种情况缺省 `default="(status: boolean)"` 则默认为不显示
     *
     * @author HanskiJay
     * @since  2021-12-21
     * @param  string      $str 需要解析的文本
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
                $strings[] = ($display) ? $area : Str::findTagNewline($area, $str);
                $replace[] = ($display) ? $original : '';
            }
            $str = str_replace($strings, $replace, $str);
        }
    }

    /**
     * 将模板语法替换成存在的函数方法并调用返回结果
     *
     * ~Usage: {function_name|arguments...}
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @param  string $str
     * @return void
     */
    protected function functionReplace(string &$str) : void
    {
        $regex = '/{(\w+)\|(.*)}/iuU';
        if(preg_match_all($regex, $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[0] as $k => $v)
            {
                $function = $matches[1][$k];
                if(!function_exists($function)) {
                    continue;
                }

                try {
                    $values = preg_split('/, |,/iU', $matches[2][$k]);
                    $values = array_filter($values);
                    $tmp    = [];
                    foreach($values as $value) {
                        if(strpos($value, '$') !== false) {
                            $value = substr($value, 1, strlen($value));
                            if(!is_null($value = $this->getAssign($value))) {
                                $tmp[] = $value;
                                continue;
                            }
                        }
                        $tmp[] = $value;
                    }
                    $values = $tmp;
                    unset($tmp);
                    // ReflectionFunction 反射类获取 ReflectionParameter 有问题, 因此跳过做类型检测;
                    $strings[] = $v;
                    $replace[] = $function(...$values);
                } catch(\Error $e) {
                    $strings[] = $v;
                    $replace[] = "[F-ERROR: {$function}] " . $e->getMessage();
                }
            }
            $str = str_replace($strings, $replace, $str);
        }
    }

    /**
     * 解析 OwO语句
     *
     * ~Usage: <owo type="component" src="资源路径" />
     * ~Usage: see View->setParentTemplate($filePath: string, $update: boolean)
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
                        $path      = Path::getComponentPath($matches[3][$k]);
                        $replace[] = Path::getFile($path);
                    break;

                    case 'childComponent':
                        $strings[] = $matches[0][$k];
                        $replace[] = $this->childTemplate;
                    break;

                    default:
                        $strings[] = Str::findTagNewline($matches[0][$k], $str);
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
     * @author HanskiJay
     * @since  2020-09-10
     * @param bool $update
     * @return string
     */
    public function render(bool $update = false) : string
    {
        if($this->isRendered() && !$update) {
            return $this->viewTemplate;
        }
        if(is_null($this->viewTemplate)) {
            $this->init(true);
        }

        // 第一次调用, 防止使用者在OwO模板语句中写入变量;
        $this->replaceBindValues($this->viewTemplate);
        // 解析OwO语句;
        $this->parseOwOSentence($this->viewTemplate);
        // 转换常量绑定;
        if(preg_match_all('/{([0-9A-Z_]*)}/mU', $this->viewTemplate, $matches)) {
            $strings = $replace = [];
            foreach($matches[1] as $k => $constName) {
                if(defined($constName) || isset($this->constants[$constName])) {
                    $strings[] = $matches[0][$k];
                    $replace[] = @constant($constName) ?? $this->constants[$constName];
                }
            }
            $this->viewTemplate = str_replace($strings, $replace, $this->viewTemplate);
            $strings = $replace = []; // 重置;
        }
        // 解析绑定数组;
        $this->replaceBindArrays($this->viewTemplate);
        // 解析循环语句;
        changeType(_global('view.loopLevel', 3), $l);
        for($i = null; $i <= $l; $i++) {
            $this->parseLoopArea($this->viewTemplate, $i);
        }
        // 绑定变量;
        $this->replaceBindValues($this->viewTemplate);
        // 解析@display语法;
        $this->parseDisplayArea($this->viewTemplate);
        // 解析IF-ELSE语法区域;
        changeType(_global('view.judgementLevel', 3), $l);
        for($i = null; $i <= $l; $i++) {
            $this->parseJudgementArea($this->viewTemplate, $i);
        }
        // 解析模板语法之函数调用;
        $this->functionReplace($this->viewTemplate);
        // 绑定资源路径到路由;
        Path::parseResourcePath($this->bindValues, $this->customPath, $this->viewTemplate);

        $this->isRendered = true;
        return $this->viewTemplate;
    }


    /**
     * 返回模板渲染状态
     *
     * @author HanskiJay
     * @since  2022-08-02
     * @return boolean
     */
    final public function isRendered() : bool
    {
        return $this->isRendered;
    }

    /**
     * 返回模板扩展名称
     *
     * @author HanskiJay
     * @since  2022-08-02
     * @return string
     */
    final public static function getExtensionName() : string
    {
        return static::$extensionName ?? '.html';
    }

    /**
     * 返回模板
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @return string|null
     */
    final public function &getTemplate() : ?string
    {
        return $this->viewTemplate;
    }


    /**
     * 魔术方法: 直接从绑定变量数组中返回值 (当键名存在时)
     *
     * @author HanskiJay
     * @since  2022-08-01
     * @param  string $index
     * @return mixed|null
     */
    final public function __get(string $index)
    {
        return $this->getAssign($index);
    }

    /**
     * 魔术方法: 直接将变量设置进绑定变量数组
     *
     * @author HanskiJay
     * @since  2022-08-01
     * @param  string $index
     * @return void
     */
    final public function __set(string $index, $val) : void
    {
        $this->assign($index, $val);
    }

    /**
     * 魔术方法: 直接从绑定变量数组中删除值 (当键名存在时)
     *
     * @author HanskiJay
     * @since  2022-08-01
     * @param  string $index
     * @return void
     */
    final public function __unset(string $index) : void
    {
        $this->unassign($index);
    }
}