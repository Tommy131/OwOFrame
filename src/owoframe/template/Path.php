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
 * @Date         : 2023-02-05 23:20:14
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-19 23:29:02
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\template;



use owoframe\http\route\Route;
use owoframe\utils\MIMEType;

class Path
{
    /**
     * 文件路径
     *
     * @var [type]
     */
    public static $filePath;


    public function __construct(string $filePath)
    {
        static::$filePath = $filePath;
    }

    /**
     * 判断路径或文件是否存在
     *
     * @param  string  $path
     * @return boolean
     */
    public static function exists(string $path) : bool
    {
        return is_dir($path) || is_file($path);
    }

    /**
     * 代理方法
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function view(string $name, bool $backSlash = false, &$isExists = false) : string
    {
        \owo\str_escape($name);
        $name     = static::$filePath . $name . ($backSlash ? DIRECTORY_SEPARATOR : '');
        $isExists = self::exists($name);
        return $name;
    }

    /**
     * 返回模板组件路径
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function component(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return self::view('component/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回Application局部静态资源路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function app_static(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return self::view('static/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回CSS文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function css(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return self::app_static('css/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回JS文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function js(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return self::app_static('js/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回图片文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function img(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return self::app_static('img/' . $name, $backSlash, $isExists);
    }



    /**
     * 返回公共资源文件夹下的CSS文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function public_css(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return \owo\static_path('css/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回公共资源文件夹下的JS文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function public_js(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return \owo\static_path('js/' . $name, $backSlash, $isExists);
    }

    /**
     * 返回公共资源文件夹下的图片文件目录的指定路径/文件
     *
     * @param  string  $name
     * @param  boolean $backSlash
     * @param  boolean $isExists
     * @return string
     */
    public static function public_img(string $name = '', bool $backSlash = false, &$isExists = false) : string
    {
        return \owo\static_path('img/' . $name, $backSlash, $isExists);
    }


    /**
     * ~ 模板渲染核心方法
     */
    /**
     * 解析模板中的资源路径绑定
     * ~ usage  <img src="{IMG|File_Path}" @htc="controlName">
     * ~ usage  <script src="{JS|File_Path}" @htc="controlName">
     * ~ usage  <link src="{CSS|File_Path}" @htc="controlName">
     * ~ usage  @htc: 后端控制显示的ID
     *
     * @param  array  $bindValues 绑定标签数组
     * @param  array  $customPath 自定义路径定义
     * @param  string &$template  传入模板
     * @return void
     */
    public static function parseResourcePath(array $bindValues, array $customPath, string &$template) : void
    {
        $regex =
        [
            '/<(img|script|link) (.*)>/imuU',
            '/@htc="(\w*)"/muU',
            '/{(\w*)\|(.*)}/imuU'
        ];

        $strings = $replace =  [];
        if(preg_match_all($regex[0], $template, $matches))
        {
            foreach($matches[0] as $k => $tag) {
                // 调用状态控制
                if(preg_match($regex[1], $tag, $m)) {
                    $status = $bindValues[View::HTML_TAG_CONTROL_PREFIX . $m[1]] ?? false;

                    // 判断空置状态, 若为 false 则将标签替换为空
                    if(!$status) {
                        $strings[] = \owo\get_new_line($tag, $template);
                        $replace[] = '';
                    } else {
                        // 将标签中的 @htc="controlName" 替换为空
                        $strings[] = $tag;
                        $replace[] = str_replace($m[0], '', $tag);
                    }
                }
            }
        }

        // 绑定标签到静态资源路径
        if(preg_match_all($regex[2], $template, $matches)) {
            foreach($matches[0] as $k => $tag) {
                $path = self::take($matches[1][$k], $customPath);
                $src  = self::generateStaticUrl($path . DIRECTORY_SEPARATOR . $matches[2][$k]);
                $strings[] = $matches[0][$k];
                $replace[] = $src;
            }
        }
        // optimized: 集中替换节约内存开支
        $template = str_replace($strings, $replace, $template);
    }

    /**
     * 生成静态资源路由地址
     *
     * @param  string $filePath 静态资源文件路径
     * @return string
     */
    protected static function generateStaticUrl(string $filePath) : string
    {
        \owo\str_escape($filePath);
        $type = explode('.', $filePath);
        $type = strtolower(end($type));

        if(is_file($filePath) && MIMEType::exists($type))
        {
            $basePath = \owo\cache_path($type, true);
            if(!is_dir($basePath)) mkdir($basePath, 755, true);
            $hashTag   = md5($filePath);
            $basePath .= "{$hashTag}.php";

            if(!file_exists($basePath)) {
                $proxyFile = file_get_contents(\owo\s_template_path('ProxyFileTemplate.php'));
                if($proxyFile) {
                    $contentType  = MIMEType::get($type);
                    $contentType .= (\owo\str_has($contentType, 'application') || \owo\str_has($contentType, 'text')) ? '; charset=UTF-8' : '';
                    $proxyFile    = str_replace(['{time}', '{date}', '{contentType}', '{filePath}'], [date('H:i:s'), date('Y-m-d'), $contentType, $filePath], $proxyFile);
                    file_put_contents($basePath, $proxyFile);
                }
            }
            $filePath = '/' . Route::TAG_STATIC_ROUTE . "/{$type}/{$hashTag}";
        } else {
            $filePath = null;
        }
        return $filePath ?? '(unknown)';
    }

    /**
     * 通过资源定义常量返回路径
     *
     * @param  string $type
     * @param  array  $customPath
     * @return string
     */
    public static function take(string $type, array $customPath = []) : string
    {
        switch(strtoupper($type))
        {
            case 'CSS':
            case 'CSSPATH':
                $path = self::css();
            break;
            case 'RCSS':
            case 'RCSSPATH':
                $path = self::public_css();
            break;

            case 'JS':
            case 'JSPATH':
                $path = self::js();
            break;
            case 'RJS':
            case 'RJSPATH':
                $path = self::public_js();
            break;

            case 'IMG':
            case 'IMGPATH':
                $path = self::img();
            break;
            case 'RIMG':
            case 'RIMGPATH':
                $path = self::public_img();
            break;

            case 'PACKAGE':
            case 'PKGPATH':
                $path = self::app_static();
            break;

            default:
                if(isset($customPath[$type])) {
                    $path = $customPath[$type] . DIRECTORY_SEPARATOR;
                }
            break;
        }
        return $path ?? "[OwOView-Error] Type {$type} not found";
    }

    /**
     * 通过限制文件大小获取文件
     *
     * @param  string  $filePath
     * @param  integer $maxSize
     * @return string
     */
    public static function getFile(string $filePath, int $maxSize = 10240) : string
    {
        if(!self::exists($filePath)) {
            return "File {$filePath} not found";
        }
        clearstatcache(true, $filePath);
        if(round(filesize($filePath)) > $maxSize) {
            return "The file {$filePath} size is exceeds maximum allowed size.";
        }
        return file_get_contents($filePath);
    }
}
?>