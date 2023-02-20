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
 * @Date         : 2023-02-05 15:25:18
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-09 20:45:51
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\template;



use Error;
use owoframe\exception\FileNotFoundException;
use owoframe\exception\OwOFrameException;

class Template
{
    /**
     * 渲染状态
     *
     * @access private
     * @var boolean
     */
    private $isRendered = false;

    /**
     * 渲染状态
     *
     * @access protected
     * @var boolean
     */
    protected $fileExists = false;

    /**
     * 变量绑定池
     *
     * @var array
     */
    protected $assigned = [];

    /**
     * 常量绑定池
     *
     * @var array
     */
    protected $constants = [];

    /**
     * 模板文件路径
     *
     * @var string
     */
    protected $filePath;

    /**
     * 模板文件名称
     *
     * @var string
     */
    protected $fileName;

    /**
     * 模板文件名称
     *
     * @var string
     */
    protected $fullName;

    /**
     * 文件资源路径管理类
     *
     * @var Path
     */
    protected $Path = null;

    /**
     * 视图模板
     *
     * @access protected
     * @var string
     */
    protected $viewTemplate = null;


    /**
     * 初始化模板引擎
     *
     * @param  string      $file
     * @param  string|null $path
     */
    public function __construct(string $file = '', ?string $path = null)
    {
        $lastCall = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $_        = array_shift($lastCall);
        $lastCall = array_shift($lastCall) ?? $_;
        // 文件名为空时自动识别文件名
        if($file === '') {
            if($lastCall['function'] === '__construct') {
                $fileName = explode('\\', $lastCall['class']);
                $fileName = end($fileName);
            }
            $file = ucfirst(strtolower($fileName ?? $lastCall['function'])) . '.html';
        } else {
            $file = basename($file);
        }

        if(!$path) {
            $path = dirname($_['file'] ?? $lastCall['file'], 2) . '/view/';
            \owo\str_escape($path, false);
        }

        $this->setFileName($file, false)->setFilePath($path);
        $this->Path = new Path($this->filePath);
    }

    /**
     * 更新模板文件路径
     *
     * @return boolean
     */
    public function updateFile() : bool
    {
        $this->fileExists = false;
        $this->fullName = $this->filePath . $this->fileName;

        if(!file_exists($this->fullName)) {
            throw new FileNotFoundException($this->fullName);
        }

        $this->fileExists = true;
        return $this->fileExists;
    }

    /**
     * 更新模板文件名称
     *
     * @param  string|null $path
     * @param  boolean     $checkUpdate
     * @return Template
     */
    public function setFilePath(?string $path, bool $checkUpdate = true) : Template
    {
        $this->filePath = $path;
        if($checkUpdate) $this->updateFile();
        return $this;
    }

    /**
     * 更新模板文件名称
     *
     * @param  string   $name
     * @param  boolean  $checkUpdate
     * @return Template
     */
    public function setFileName(string $name, bool $checkUpdate = true) : Template
    {
        $this->fileName = $name;
        if($checkUpdate) $this->updateFile();
        return $this;
    }

    /**
     * 加载模板
     *
     * @param  boolean  $update
     * @return Template
     */
    public function load(bool $update = false) : Template
    {
        if($this->fileExists && ($update || !$this->viewTemplate)) {
            $this->viewTemplate = file_get_contents($this->getFullName());
        }
        return $this;
    }

    /**
     * 合并常量定义
     *
     * @return Template
     */
    public function mergeConstants(array $arr) : Template
    {
        $this->constants = array_merge($this->constants, $arr);
        return $this;
    }

    /**
     * 绑定变量到模板
     *
     * @param  string|array $index
     * @param  mixed|null   $val
     * @return Template
     */
    public function assign($index, $val = null) : Template
    {
        if(is_array($index)) {
            $this->assigned = array_merge($this->assigned, $index);
        }
        elseif(is_string($index)) {
            if(!is_null($val)) {
                $this->assigned[$index] = $val;
            }
        }

        return $this;
    }

    /**
     * 解除绑定模板中的变量定义
     *
     * @param  string $index
     * @return Template
     */
    public function unassign(string $index) : Template
    {
        if(isset($this->assigned[$index])) {
            unset($this->assigned[$index]);
        }
        return $this;
    }

    /**
     * 返回文件路径+名称
     *
     * @return string
     */
    public function getFullName() : string
    {
        return $this->fullName;
    }


    #-----------------------------------------------------------------------------------#
    /**
     * OwOView引擎核心解析区域
     *
     * @author HanskiJay
     */
    #-----------------------------------------------------------------------------------#
    /**
     * 替换常量绑定
     *
     * ~ Usage: {CONSTANT}
     *
     * @param  string &$str
     * @return Template
     */
    protected function replaceConstants(string &$str) : Template
    {
        if(preg_match_all('/{([0-9A-Z_]*)}/mU', $str, $matches)) {
            $strings = $replace = [];
            foreach($matches[1] as $k => $const) {
                if(defined($const) || isset($this->constants[$const])) {
                    $strings[] = $matches[0][$k];
                    $replace[] = @constant($const) ?? ($this->constants[$const] ?? null);
                }
            }
            $str = str_replace($strings, $replace, $str);
        }
        return $this;
    }

    /**
     * 替换绑定变量
     *
     * ~ Usage: {$bindValue}
     * ~ Usage: {$bindValue|defaultOutput}
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  string &$str
     * @return Template
     */
    protected function replaceAssigned(string $key, $value, string &$str) : Template
    {
        if(preg_match('/{\$' . $key . '\|(.*)}/muU', $str, $match)) {
            if(is_bool($value)) {
                \owo\bool2str($value);
            }
            $str = str_replace($match[0], $value ?? \owo\str_check_null($match[1]), $str);
        }
        $str = str_replace("{\${$key}}", $value, $str);
        return $this;
    }

    /**
     * 替换绑定变量
     *
     * ~ Usage: {$bindValue}
     * ~ Usage: {$bindValue|defaultOutput}
     *
     * @param  string &$str
     * @return Template
     */
    protected function replaceAllAssigned(string &$str) : Template
    {
        if(preg_match_all('/{\$(\w+)(\|(.*))?}/muU', $str, $matches))
        {
            $strings = $replace = [];
            foreach($matches[1] as $k => $bindTag)
            {
                $strings[] = $matches[0][$k];
                // 从绑定变量数组中获取绑定值
                $result = $this->{$bindTag};
                if($result) {
                    $replace[] = $result;
                } else {
                    // 判断是否存在默认值
                    $replace[] = isset($matches[3][$k]) ? \owo\str_check_null($matches[3][$k]) : $matches[0][$k];
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
     * @param  string &$str
     * @return Template
     */
    protected function replaceArray(string &$str) : Template
    {
        if(preg_match_all('/{\$(\w+)\.(\w+)(\|(.*))?}/muU', $str, $matches))
        {
            $strings = $replace = [];
            foreach($matches[1] as $k => $bindTag)
            {
                $strings[] = $matches[0][$k];
                // 从绑定变量数组中获取绑定值
                $result = $this->{$bindTag};
                if($result) {
                    $key = $matches[2][$k];
                    if(isset($result[$key])) {
                        $replace[] = $result[$key];
                    } else {
                        // 判断是否存在默认值
                        $replace[] = (isset($matches[4][$k])) ? \owo\str_check_null($matches[4][$k]) : $matches[0][$k];
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
     * 将模板语法替换成存在的函数方法并调用返回结果
     *
     * ~ Usage: {function_name|arguments...}
     *
     * @param  string &$str
     * @return Template
     */
    protected function replaceFunction(string &$str) : Template
    {
        $regex = '/{(\w+)\|(.*)}/iuU';
        if(preg_match_all($regex, $str, $matches))
        {
            $strings = $replace = [];
            foreach($matches[0] as $k => $v)
            {
                $function = $matches[1][$k];
                if(!function_exists($function))
                {
                    $tmp = '\\owo\\' . $function;
                    if(function_exists($tmp)) {
                        $function = $tmp;
                    } else {
                        continue;
                    }
                }

                try {
                    $values = preg_split('/, |,/iU', $matches[2][$k]);
                    $values = array_filter($values);
                    $tmp    = [];
                    foreach($values as $value) {
                        if(strpos($value, '$') !== false) {
                            $value = substr($value, 1, strlen($value));
                            $value = $this->{$value};
                            if(!is_null($value)) {
                                $tmp[] = $value;
                                continue;
                            }
                        }
                        $tmp[] = $value;
                    }
                    $values = $tmp;
                    unset($tmp);
                    // ReflectionFunction 反射类获取 ReflectionParameter 有问题, 因此跳过做类型检测
                    $strings[] = $v;
                    $replace[] = $function(...$values);
                } catch(Error $e) {
                    $strings[] = $v;
                    $replace[] = "[F-ERROR: {$function}] " . $e->getMessage();
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
            $bindTag  = trim($loopGroup[2]);                // 绑定的数组变量到模板
            $defined  = trim($loopGroup[1]);                // 定义的变量到模板
            // $loopArea = trim(preg_replace("/{$loopEnd}/im", '', preg_replace("/{$loopHead}/im", '', $loopArea)));
            // $loopArea = explode("\n", trim($loopArea));      // 匹配到的循环语句
            $loop     = explode("\n", trim($loopGroup[0]));      // 匹配到的循环语句

            $data = $this->{$bindTag};
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
                    throw new OwOFrameException('Illegal usage caused in Template->parseLoopArea() at line ' . __LINE__);
                }

                foreach($loop as $n => $line) {
                    if(preg_match("/({$loopEnd}|{$loopHead})/im", $line)) {
                        continue;
                    }
                    $line   = trim($line);
                    $length = strlen($line);
                    if(preg_match_all("/{$defined}?([\\\.]?[a-zA-Z0-9]){0,$length}/", $line, $match)) {
                        $matchedTags = array_shift($match);    // 获取到的原始绑定标签集
                        foreach($matchedTags as $matchedTag) {
                            $parseArray = explode('.', $matchedTag); // 解析并分级绑定标签
                            array_shift($parseArray);                // 去除第一级原始绑定标签

                            $cnum = count($data);
                            if((count($parseArray) === 0) && ($cnum > 1)) {
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
     * @param  string $type
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
     * ~ Usage  {if condition1 Judgement condition2 and|&& condition3 Judgement condition4...}
     * @see    Tested picture in /tests/Function_[View-parseJudgementArea()]_test_log.png
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
                            $matched[1] = substr($matched[1], 1, strlen($matched[1]));
                            $matched[1] = $this->{$matched[1]};
                        }
                        if(strpos($matched[3], '$') !== false) {
                            $matched[3] = substr($matched[3], 1, strlen($matched[3]));
                            $matched[1] = $this->{$matched[3]};
                        }
                        if(is_string($matched[1])) {
                            \owo\str($matched[1]);
                        }
                        if(is_string($matched[3])) {
                            \owo\str($matched[3]);
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
                        throw new OwOFrameException("'Grammar mistake: Invalid Judgement Sentence '{$v}''");
                    }
                }
                $replace[] = ($lastResult ?? $result) ? $matches[2][$k] : trim($matches[3][$k] ?? '', "\r\n\t");
            }
            $str = str_replace($strings, $replace, $str);
        }
    }





    /**
     * 渲染视图到前端
     *
     * @param  boolean $update
     * @return string
     */
    public function render(bool $update = false) : string
    {
        if($this->isRendered() && !$update) {
            return $this->viewTemplate;
        }
        if(!$this->viewTemplate) {
            $this->load(true);
        }

        $this->replaceAllAssigned($this->viewTemplate)->replaceConstants($this->viewTemplate)->replaceArray($this->viewTemplate);

        // 解析循环语句
        $l = \owo\_global('view.loopLevel', 3);
        for($i = null; $i <= $l; $i++) {
            $this->parseLoopArea($this->viewTemplate, $i);
        }
        // 解析IF-ELSE语法区域
        $l = \owo\_global('view.judgementLevel', 3);
        for($i = null; $i <= $l; $i++) {
            $this->parseJudgementArea($this->viewTemplate, $i);
        }
        $this->replaceFunction($this->viewTemplate);

        $this->isRendered = true;
        return $this->viewTemplate;
    }



    /**
     * 返回模板渲染状态
     *
     * @return boolean
     */
    final public function isRendered() : bool
    {
        return $this->isRendered;
    }

    /**
     * 返回模板
     *
     * @return string|null
     */
    final public function &getTemplate() : ?string
    {
        return $this->viewTemplate;
    }



    #-------------------------------#
    #            魔术方法            #
    #-------------------------------#
    /**
     * 魔术方法: 直接从绑定变量数组中返回值 (当键名存在时)
     *
     * @param  string $index
     * @return mixed|null
     */
    final public function __get(string $index)
    {
        return $this->assigned[$index] ?? null;
    }

    /**
     * 魔术方法: 直接将变量设置进绑定变量数组
     *
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
     * @param  string $index
     * @return void
     */
    final public function __unset(string $index) : void
    {
        $this->unassign($index);
    }
}
?>