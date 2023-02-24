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
 * @Date         : 2023-02-01 20:34:03
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-24 03:24:00
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 * @description  : 此文件仅负责提供框架底层调度方法
 */
declare(strict_types=1);



namespace owo
{
    use Closure;
    use Composer\Autoload\ClassLoader;
    use owoframe\object\INI;
    use owoframe\object\Pipe;
    use owoframe\utils\TextColorOutput as TCO;

    #-------------------------------------------------------------#
    #                       系统基本处理函数                       #
    #-------------------------------------------------------------#
    if(!defined('OS_ANDROID')) define('OS_ANDROID', 'android');
    if(!defined('OS_LINUX'))   define('OS_LINUX',   'linux');
    if(!defined('OS_WINDOWS')) define('OS_WINDOWS', 'windows');
    if(!defined('OS_MACOS'))   define('OS_MACOS',   'mac');
    if(!defined('OS_BSD'))     define('OS_BSD',     'bsd');
    if(!defined('OS_UNKNOWN')) define('OS_UNKNOWN', 'unknown');

    /**
     * 返回操作系统名称
     *
     * @return string
     */
    function get_os() : string
    {
        $os = php_uname('s');
        if(stri_has($os, 'linux')) {
            $os = @file_exists('/system/build.prop') ? OS_ANDROID : OS_LINUX;
        }
        elseif(stri_has($os, 'windows')) {
            $os = OS_WINDOWS;
        }
        elseif((stri_has($os, 'mac')) || (stri_has($os, 'darwin'))) {
            $os = OS_MACOS;
        }
        elseif(stri_has($os, 'bsd')) {
            $os = OS_BSD;
        }
        return $os ?? OS_UNKNOWN;
    }

    /**
     * 打开文件按路径
     *
     * @param  string $path
     * @return void
     */
    function open(string $path) : void
    {
        if(file_exists($path) && (get_os() === OS_WINDOWS)) {
            system('start ' . $path);
        }
    }

    /**
     * 返回类加载器
     *
     * @param  boolean $name
     * @return ClassLoader
     */
    function get_class_loader(bool $update = false) : ClassLoader
    {
        global $classLoader;
        static $_;
        if($update && ($classLoader instanceof ClassLoader)) {
            if(!$_ instanceof $classLoader) {
                $_ = $classLoader;
            }
        }
        return $classLoader ?? $_;
    }

    /**
     * 设置类加载器
     *
     * @param  ClassLoader $cl
     * @return void
     */
    function set_class_loader(ClassLoader $cl) : void
    {
        global $classLoader;
        if($cl instanceof ClassLoader) {
            $classLoader = $cl;
            get_class_loader(true);
        }
    }

    /**
     * 获取当前PHP的运行模式
     *
     * @return string
     */
    function php_current() : string
    {
        $sapi = str_split(php_sapi_name(), null, '-');
        $sapi = array_shift($sapi);
        return !is_string($sapi) ? 'error' : $sapi;
    }

    /**
     * 判断当前PHP的运行模式是否为CLI
     *
     * @return boolean
     */
    function php_is_cli() : bool
    {
        return stri_has(php_current(), 'cli');
    }

    /**
     * 判断当前PHP的运行模式是否为CGI
     *
     * @return boolean
     */
    function php_is_cgi() : bool
    {
        return stri_has(php_current(), 'cgi');
    }

    /**
     * 返回系统初始化到调用此函数的总共运行时间
     *
     * @param  integer $round
     * @return float
     */
    function runtime(int $round = 7) : float
    {
        return round(microtime(true) - START_MICROTIME, $round);
    }

    /**
     * 返回真实内存占用 (MB)
     *
     * @return float
     */
    function get_used_memory() : float
    {
        return round((memory_get_usage(true) - MEMORY_USAGE) / pow(1024, 3), 4);
    }

    /**
     * 返回简化的对象类名
     *
     * @param  object $class
     * @return string
     */
    function class_short_name(object $class) : string
    {
        return basename(str_replace('\\', '/', get_class($class)));
    }

    /**
     * \返回解析后类的命名空间
     *
     * @param  string $className
     * @return string
     */
    function class_parse_namespace(string $className) : string
    {
        $className = explode('\\', $className);
        $className = array_slice($className, 0, count($className) - 1);
        return implode('\\', $className);
    }

    /**
     * 随机生成指定长度的字符串
     *
     * @param  integer $length
     * @param  boolean $specialChars
     * @return string
     */
    function random_string(int $length = 12, bool $specialChars = false) : string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if($specialChars) {
            $chars .= '!@#$%^&*()';
        }
        $result = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, $max)];
        }
        return $result;
    }

    /**
     * 生成一个UUID
     *
     * @return string
     */
    function uuid() : string
    {
        $str   = md5(uniqid(random_string(5), true));
        $uuid  = '';
        $array = [0, 8, 12, 16, 20];
        foreach([8, 4, 4, 4, 12] as $k => $v) {
            $uuid .= substr($str, $array[$k], $v) . '-';
        }
        $uuid = rtrim($uuid, '-');
        return $uuid;
    }

    /**
     * 获取变量名称
     *
     * @param  variable   $var
     * @param  array|null $scope
     * @return void
     */
    function get_variable_name(&$var, ?array $scope = null)
    {
        $scope = ($scope === null) ? $GLOBALS : $scope;
        $___OWO_TEMP_VARIABLE___ = $var;

        $var  = 'tmp_value_' . mt_rand();
        $name = array_search($var, $scope, true);

        $var = $___OWO_TEMP_VARIABLE___;
        return $name;
    }



    #-------------------------------------------------------------#
    #                     系统文件/目录处理函数                     #
    #-------------------------------------------------------------#
    /**
     * 删除文件夹 (包含嵌套)
     *
     * ! 高风险! 谨慎使用! 安全起见, 仅限CLI模式下使用!
     *
     * @param  string  $path 文件夹路径
     * @return boolean
     */
    function remove_dir(string $path) : bool
    {
        if(!php_is_cli()) {
            output('Illegal Called: ' . __FUNCTION__ . '() >> Please call the function on php-cli.');
            return false;
        }

        if(!is_dir($path)) return false;
        $path     = trim($path, '/\\') . DIRECTORY_SEPARATOR;
        $dirArray = scandir($path);
        unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);
        foreach($dirArray as $fileName)
        {
            if(is_dir($path . $fileName)) {
                remove_dir($path . $fileName);
                if(is_dir($path . $fileName)) {
                    rmdir($path . $fileName);
                }
            } else {
                unlink($path . $fileName);
            }
        }
        rmdir($path);
        return !is_dir($path);
    }

    /**
     * 删除调用者目录下的所有文件
     *
     * ! 高风险! 谨慎使用! 安全起见, 仅限CLI模式下使用!
     *
     * @param  integer $second
     * @return void
     */
    function unlink_glob(int $second = 10) : void
    {
        if(!php_is_cli()) {
            output('Illegal Called: ' . __FUNCTION__ . '() >> Please call the function on php-cli.');
            return;
        }
        $path = getcwd();
        pipe_ask("§3Are you sure to delete all files in '§7{$path}§3'?", ['Y', 'y', 'YES', 'yes'])
        ->setExpectingResult($path)
        ->then('§3Please enter the same path as above to confirm deletion:')
        ->finally(function(object $obj) use ($path, $second)
        {
            if($obj->hasLastResult()) {
                color_output('§3Confirmed! All files will be deleted after §7' . $second . '§3 seconds, Can never be undone! This operation is irreversible! (To terminate: Ctrl + C)');
                sleep($second);
                array_map('unlink', glob('*'));
                color_output("§5The file at path '§7{$path}§5' has been deleted.");
            } else {
                color_output('§1Cancelled.');
            }
        });
    }

    /**
     * 路径代理方法
     *
     * @param  string  $root
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function path_proxy(string $path, string $name = '', bool $backSlash = false) : string
    {
        str_escape($path);
        str_escape($name);
        return $path . DIRECTORY_SEPARATOR . $name . ($backSlash ? DIRECTORY_SEPARATOR : '');
    }

    /**
     * 从根目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function root_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(ROOT_PATH, $name, $backSlash);
    }

    /**
     * 从系统源代码目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function owo_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(root_path('src/owoframe'), $name, $backSlash);
    }

    /**
     * 从系统模板目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function s_template_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(owo_path('template/default'), $name, $backSlash);
    }

    /**
     * 从公共资源目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function public_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(root_path('public'), $name, $backSlash);
    }

    /**
     * 从公共资源目录返回静态资源目录下的文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function static_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(public_path('static'), $name, $backSlash);
    }

    /**
     * 从应用程序目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function application_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(root_path('application'), $name, $backSlash);
    }

    /**
     * 从存储目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function storage_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(root_path('storage'), $name, $backSlash);
    }

    /**
     * 从应用程序存储目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function app_storage_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(storage_path('application'), $name, $backSlash);
    }

    /**
     * 从模块目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function module_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(storage_path('module'), $name, $backSlash);
    }

    /**
     * 从系统目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function system_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(storage_path('system'), $name, $backSlash);
    }

    /**
     * 从系统配置文件目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function config_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(system_path('config'), $name, $backSlash);
    }

    /**
     * 从系统日志目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function log_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(system_path('logs'), $name, $backSlash);
    }

    /**
     * 从系统缓存目录返回指定文件夹/文件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @return string
     */
    function cache_path(string $name = '', bool $backSlash = false) : string
    {
        return path_proxy(system_path('cache'), $name, $backSlash);
    }

    /**
     * 创建存储目录文件夹
     *
     * @return void
     */
    function create_paths() : void
    {
        if(!is_dir(static_path()))       mkdir(static_path(),       755, true);
        if(!is_dir(application_path()))  mkdir(application_path(),  755, true);
        if(!is_dir(storage_path()))      mkdir(storage_path(),      755, true);
        if(!is_dir(app_storage_path()))  mkdir(app_storage_path(),  755, true);
        if(!is_dir(system_path()))       mkdir(system_path(),       755, true);
        if(!is_dir(module_path()))       mkdir(module_path(),       755, true);
        if(!is_dir(config_path()))       mkdir(config_path(),       755, true);
        if(!is_dir(log_path()))          mkdir(log_path(),          755, true);
        if(!is_dir(cache_path()))        mkdir(cache_path(),        755, true);

        add_gitignore(\owo\application_path());
        add_gitignore(\owo\storage_path());
        add_gitignore(\owo\app_storage_path());
        add_gitignore(\owo\module_path(), ['!/example', '!/example/*'], true);
        add_gitignore(\owo\config_path());
        add_gitignore(\owo\log_path());
        add_gitignore(\owo\cache_path());
    }

    /**
     * 添加 .gitignore 文件
     *
     * @param  string  $path
     * @param  array   $ignored
     * @param  boolean $recreate
     * @return boolean
     */
    function add_gitignore(string $path, array $ignored = [], bool $recreate = false) : bool
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.gitignore';
        if(!file_exists($path) || $recreate) {
            return file_put_contents($path, implode(PHP_EOL, array_merge(['*', '!.gitignore'], $ignored))) !== false;
        }
        return false;
    }

    /**
     * 读取全局配置文件 | get global configuration
     *
     * @param  string $index
     * @param  mixed  $default
     * @return mixed
     */
    function _global(string $index, $default = null)
    {
        global $_global;
        return ($_global instanceof INI) ? $_global->get($index, $default) : $default;
    }


    #-------------------------------------------------------------#
    #                      数据/字符串处理函数                      #
    #-------------------------------------------------------------#
    /**
     * 检查参数是否可以回调
     *
     * @param  mixed   $var
     * @param  boolean $preventInfiniteLoop 判断参数是否等于上级调用者, 防止死循环嵌套
     * @return boolean
     */
    function var_is_callable($var, bool $preventInfiniteLoop = true) : bool
    {
        return (!is_callable($var) && is_string($var) && function_exists($var)) ? ($preventInfiniteLoop ? ($var !== trim(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? '', '\\')) : true) : true;
        /* if(!is_callable($var) && is_string($var) && function_exists($var)) {
            if($preventInfiniteLoop) {
                $trace = trim(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? '', '\\');
                return $var !== $trace;
            } else {
                return true;
            }
        } else {
            return true;
        } */
    }

    /**
     * 用作在CMD & SHELL下获取标准输入的方法
     *
     * @param  string $output  向CMD & SHELL输出的显示文字
     * @param  mixed  $default 默认结果
     * @return mixed
     */
    function ask(string $output, $default = null)
    {
        if(!php_is_cli()) {
            output('Illegal Called: ' . __FUNCTION__ . '() >> Please call the function on php-cli.');
            return;
        }

        $output .= $default ? " (Default: {$default})  " : '  ';

        global $owo_system_output_allowNewLine;
        $owo_system_output_allowNewLine = false;
        color_output($output);
        $owo_system_output_allowNewLine = true;

        $_ = fgets(STDIN);
        return ($_ && (strlen($_) > 0)) ? trim($_) : $default;
    }

    /**
     * 使用责任链模式进行管道问询
     *
     * @param  string $question
     * @param  mixed  $expect
     * @return Pipe
     */
    function pipe_ask(string $question, $expect) : Pipe
    {
        if(!php_is_cli()) {
            output('Illegal Called: ' . __FUNCTION__ . '() >> Please call the function on php-cli.');
            return false;
        }

        $allowAll = 'any';
        static $object;
        if(!$object) {
            $object = new Pipe('\\owo\\ask');
            $object->setResultCheckerCallback(function($result) use ($object, $allowAll) {
                $expect = $object->getExpectingResult();
                if(!is_array($expect)) {
                    return (is_string($expect) && is_string($result) && str_is_regex($expect) && preg_match_all($expect, $result)) ? true : ($result === $expect) || (is_string($expect) && (strtolower($expect) === $allowAll));
                } else {
                    return in_array($result, $expect);
                }
            });
        }

        if(is_array($expect)) {
            $object->setExpectingResults($expect);
        } else {
            $object->setExpectingResult($expect);
        }
        $object->then($question);
        return $object;
    }

    /**
     * 检查目标数组是否缺少某个元素 (仅限二维数组)
     *
     * @param  array   $haystack   需要检查的数组
     * @param  array   $needle     需要检查的键名
     * @param  string  $allowEmpty 允许空元素
     * @param  string  $missParam  返回缺少的参数
     * @return boolean
     */
    function array_check_validity(array $haystack, array $needle, bool $allowEmpty = true, &$missParam = null) : bool
    {
        if(!$allowEmpty) $haystack = array_filter($haystack);
        while(count($needle) > 0)
        {
            $_ = array_shift($needle);
            if(!isset($haystack[$_])) {
                $missParam = $_;
                return false;
            }
        }
        return true;
    }

    /**
     * 判断传入的数据是否已序列化
     *
     * @param  string  $haystack 需要判断的数据
     * @return boolean
     */
    function str_is_serialized(string $haystack)
    {
        $haystack = trim($haystack);
        if('N;' === $haystack) return true;
        if(!preg_match('/^([adObis]):/', $haystack, $matches)) return false;
        switch ($matches[1]) {
            case 'a':
            case 'O':
            case 's':
            if(preg_match("/^{$matches[1]}:[0-9]+:.*[;}]\$/s", $haystack)) return true;
            break;
            case 'b':
            case 'i':
            case 'd':
            if(preg_match("/^{$matches[1]}:[0-9.E-]+;\$/", $haystack)) return true;
            break;
        }
        return false;
    }

    /**
     * 判断字符串是否为正则表达式
     *
     * @param  string  $str
     * @param  string  $range 范围标识符
     * @param  string  $regex
     * @return boolean
     */
    function str_is_regex(string $str, string $range = '/', &$regex = '') : bool
    {
        $regex = str_replace($range, '/', $str);
        return (bool) preg_match('/^\\' . $range . '.*\\' . $range . '([a-z]+)?$/imu', $str);
    }

    /**
     * 判断传入的字符串是否仅为字母和数字
     *
     * @param  string  $str
     * @param  array   &$match
     * @return boolean
     */
    function str_only_letter_and_number(string $str, &$match = null) : bool
    {
        return (bool) preg_match('/^[A-Za-z0-9]+$/', $str, $match);
    }

    /**
     * 简单判断字符串是否为邮箱格式
     *
     * @param  string  $str
     * @param  string  &$suffix 允许匹配的域名后缀 (e.g.: $suffix = 'com.com.cn|abc.cn'), 匹配完成后传入匹配结果到此参数
     * @return boolean
     */
    function str_is_email(string $str, string &$suffix = '') : bool
    {
        $preset = 'com|org|net|com.cn|org.cn|net.cn|cn';
        // Judgement for the allowed suffix format;
        if(preg_match('/[a-z.|]+/i', $suffix)) {
            $preset .= '|' . $suffix;
        }
        $preset = str_replace('.', '\.', $preset);
        return (bool) preg_match('/^([\w+\-.]+)@([a-z0-9\-.]+)\.(' . $preset . ')$/i', trim($str), $suffix);
    }

    /**
     * 判断字符串中是否包含指定字符 (区分大小写)
     *
     * @param  string  $str
     * @param  string  $needle
     * @return boolean
     */
    function str_has(string $str, string $needle) : bool
    {
        return strpos($str, $needle) !== false;
    }

    /**
     * 判断字符串中是否包含指定字符 (不区分大小写)
     *
     * @param  string  $str
     * @param  string  $needle
     * @return boolean
     */
    function stri_has(string $str, string $needle) : bool
    {
        return stripos($str, $needle) !== false;
    }

    /**
     * 转义字符串
     *
     * @param  string  &$str
     * @param  boolean $trim
     * @return void
     */
    function str_escape(string &$str, bool $trim = true) : void
    {
        $str = str_replace(['/', '\\', '|'], DIRECTORY_SEPARATOR, $trim ? trim($str, '/\\|') : $str);
    }

    /**
     * 更好的字符串分割函数
     *
     * @param  string            $route
     * @param  integer|null      $level
     * @param  string            $splitter
     * @return array|string|null
     */
    function str_split(string $route, ?int $level = null, string $splitter = '/')
    {
        $_ = array_values(array_filter(explode($splitter, $route)));
        return !is_null($level) ? ($_[$level] ?? null) : $_;
    }

    /**
     * 字符串编码转码UTF-8
     *
     * @param  string $str 需要转码的字符串
     * @return void
     */
    function str2UTF8(string $str) : void
    {
        if(MB_SUPPORTED) {
            $encode = mb_detect_encoding($str, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
            $str = ($encode === 'UTF-8') ? $str : mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }

    /**
     * 智能识别字符在CLI模式下的占位 (支持中文和德语)
     * Intelligent identification of string length (support Chinese and German)
     *
     * @param  string  $str
     * @return integer
     */
    function str_length(string $str) : int
    {
        // 仅删除 ASCII 字符
        $_str   = preg_replace('/[\x00-\x7F]+/', '', $str);
        // 仅保留 ASCII 字符
        $__str  = preg_replace('/[^\x00-\x7F]+/', '', $str);
        $length = strlen($__str);

        // 获取所有 3 Bytes 字符 (e.g. 中文)
        if(preg_match_all('/[^\x00-\xff]/ium', $_str, $matched)) {
            $length += 2 * count($matched[0]);
        }
        // 支持德语特殊字符
        elseif(preg_match_all('/[äöüÄÖÜß]/ium', $_str, $matched)) {
            $length += count($matched[0]);
        } else {
            $length += strlen($_str);
        }
        return (int) $length;
    }

    /**
     * 自动填充字符串到指定长度
     *
     * @param  string  $str
     * @param  integer $length
     * @param  string  $fillWith
     * @param  integer $mode     0: 从左边添加 | 1: 从右边添加 (默认)
     * @return string
     */
    function str_fill_length(string $str, int $length, string $fillWith = ' ', int $mode = 1) : string
    {
        $oLength = str_length($str);
        if($oLength < $length) {
            if(strlen($fillWith) === 0) {
                $fillWith = ' ';
            }

            $filled = '';
            for($i = $oLength; $i < $length; $i++) {
                $filled .= $fillWith;
            }
            if($mode === 1) {
                $str .= $filled;
            } else {
                $str = $filled . $str;
            }
        }
        return $str;
    }

    /**
     * 将整型转换为布尔值
     *
     * @param  integer &$num
     * @return void
     */
    function int2bool(int &$num) : void
    {
        settype($num, 'boolean');
    }

    /**
     * 将布尔值转换为字符串
     *
     * @param  boolean &$bool
     * @return void
     */
    function bool2str(bool &$bool) : void
    {
        settype($bool, 'string');
    }

    /**
     * 将字符串转换为布尔值
     *
     * @param  string  &$str
     * @return boolean
     */
    function str2bool(string &$str) : bool
    {
        if(stri_has($str, 'false')) {
            $str = false;
        } else {
            settype($str, 'boolean');
        }
        return $str;
    }

    /**
     * 自动转换字符串成别的类型
     *
     * @param  string &$str
     * @return void
     */
    function str(string &$str) : void
    {
        if(is_numeric($str)) {
            $str = preg_match('/^[0-9]+\.[0-9]+$/i', $str) ? (float) $str : (int) $str;
        }
        elseif(preg_match('/true|false/i', $str)) {
            settype($str, 'boolean');
        }
        elseif(stri_has($str, 'null')) {
            $str = null;
        }
    }

    /**
     * 返回空字符或者原始字符串
     *
     * @param  string $_
     * @return string
     */
    function str_check_null(string $_) : string
    {
        return ($_ === ':null') ? '' : $_;
    }

    /**
     * 比较两个参数的类型是否相等
     *
     * @param  mixed   $p1
     * @param  mixed   $p2
     * @return boolean
     */
    function compare_type($p1, $p2) : bool
    {
        return gettype($p1) === gettype($p2);
    }

    /**
     * 返回HTML标签与换行
     *
     * @param  string $searchString
     * @param  string $globalString
     * @return string
     */
    function get_new_line(string $searchString, string $globalString) : string
    {
        $tag = str_replace(['.', '/', '|', '$'], ['\.', '\/', '\|', '\$'], $searchString);
        if(preg_match("/(\s*?){1}{$tag}/i", $globalString, $m)) {
            return $m[0];
        }
        return $searchString;
    }



    #-------------------------------------------------------------#
    #                         系统输出函数                         #
    #-------------------------------------------------------------#
    /**
     * 使用HTML标签输出运行时间
     *
     * @return string
     */
    function html_runtime() : string
    {
        return str_replace('{runTime}', (string) runtime(), base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB6LWluZGV4OiA5OTk5OTsgYm90dG9tOiA1cHg7IHJpZ2h0OiA1cHg7Ij48Yj57cnVuVGltZX1zPC9iPjwvZGl2Pg=='));
    }

    /**
     * 以预定义格式输出内容
     *
     * @param  Closure|string $_
     * @return void
     */
    function pre($_) : void
    {
        $args = func_get_args();
        array_shift($args);

        echo '<pre>';
        if(is_array($_) || is_object($_)) {
            var_dump($_);
        }
        elseif(var_is_callable($_)) {
            $_ = $_(...$args);
        }
        // 检测之前执行的语句结果是否为字符串
        elseif(!is_string($_)) {
            var_dump($_);
        } else {
            echo $_;
        }
        echo '</pre>';
    }

    /**
     * 输出追踪栈 (Array)
     *
     * @return void
     */
    function print_debug_backtrace(int $options = DEBUG_BACKTRACE_PROVIDE_OBJECT, int $limit = 0) : void
    {
        pre('debug_backtrace', $options, $limit);
    }

    /**
     * 输出已格式化追踪栈 (#)
     *
     * @param  integer $options
     * @param  integer $limit
     * @return void
     */
    function print_debug_formatted(int $options = 0, int $limit = 0) : void
    {
        pre('debug_print_backtrace', $options, $limit);
    }

    /**
     * 输出内容
     *
     * @return void
     */
    function output() : void
    {
        global $owo_system_output_allowNewLine;
        echo implode(PHP_EOL, func_get_args()) . ($owo_system_output_allowNewLine ? PHP_EOL : '');
    }

    /**
     * 输出彩色内容
     *
     * @return void
     */
    function color_output() : void
	{
        global $owo_system_output_allowNewLine;
		echo TCO::parse(TCO::WHITE . implode(PHP_EOL, func_get_args())) . ($owo_system_output_allowNewLine ? PHP_EOL : '');
	}
}
?>