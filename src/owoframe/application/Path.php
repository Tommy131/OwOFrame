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

use owoframe\utils\Str;
use owoframe\utils\MIMEType;
use owoframe\http\HttpManager;

class Path
{



    /**
     * 模板渲染核心方法
     */
    /**
     * 解析模板中的资源路径绑定
     * ~usage  <img src="{IMG|File_Path}" @htc="controlName">
     * ~usage  <script src="{JS|File_Path}" @htc="controlName">
     * ~usage  <link src="{CSS|File_Path}" @htc="controlName">
     * ~usage  @htc: 后端控制显示的ID
     *
     * @author HanskiJay
     * @since  2021-05-25
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
                // 调用状态控制;
                if(preg_match($regex[1], $tag, $m)) {
                    $status = $bindValues[View::HTML_TAG_CONTROL_PREFIX . $m[1]] ?? false;

                    // 判断空置状态, 若为 false 则将标签替换为空;
                    if(!$status) {
                        $strings[] = Str::findTagNewline($tag, $template);
                        $replace[] = '';
                    } else {
                        // 将标签中的 @htc="controlName" 替换为空;
                        $strings[] = $tag;
                        $replace[] = str_replace($m[0], '', $tag);
                    }
                }
            }
        }


        // 绑定标签到静态资源路径;
        if(preg_match_all($regex[2], $template, $matches)) {
            foreach($matches[0] as $k => $tag) {
                $path = self::take($matches[1][$k], $customPath);
                $src  = self::generateStaticUrl($path . DIRECTORY_SEPARATOR . $matches[2][$k]);
                $strings[] = $matches[0][$k];
                $replace[] = $src;
            }
        }
        // optimized: 集中替换节约内存开支;
        $template = str_replace($strings, $replace, $template);
    }

    /**
     * 生成静态资源路由地址
     *
     * @author HanskiJay
     * @since  2021-05-29
     * @param  string            $filePath 静态资源文件路径
     * @return string
     */
    protected static function generateStaticUrl(string $filePath) : string
    {
        Str::escapeSlash($filePath);
        $type = explode('.', $filePath);
        $type = strtolower(end($type));

        if(is_file($filePath) && MIMEType::exists($type))
        {
            $mimeType = MIMEType::MIMETYPE[$type];
            $basePath = F_CACHE_PATH . $type . DIRECTORY_SEPARATOR;
            if(!is_dir($basePath)) mkdir($basePath, 755, true);
            $hashTag  = md5($filePath);
            $basePath = "{$basePath}{$hashTag}.php";

            if(!file_exists($basePath)) {
                // TODO: Cache static files;
                $charset = ((stripos($mimeType, 'application') !== false) || (stripos($mimeType, 'text') !== false)) ? 'charset=UTF-8' : '';
                file_put_contents($basePath, '<?php /* Proxy in ' . date('Y-m-d H:i:s') . "@{$hashTag} */ header('Content-Type: {$mimeType}; {$charset}'); header('X-Content-Type-Options: nosniff'); header('Cache-Control: max-age=31536000, immutable'); echo file_get_contents('{$filePath}'); ?>");
            }
            $filePath = "/static.owo/{$type}/{$hashTag}";
        } else {
            $filePath = null;
        }
        return $filePath ?? '(unknown)';
    }


    /**
     * 获取资源定义类型及返回路径
     *
     * @author HanskiJay
     * @since  2021-01-03
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
                $path = self::getStaticPath('css');
            break;
            case 'RCSS':
            case 'RCSSPATH':
                $path = self::getResourcePath('css');
            break;

            case 'JS':
            case 'JSPATH':
                $path = self::getStaticPath('js');
            break;
            case 'RJS':
            case 'RJSPATH':
                $path = self::getResourcePath('js');
            break;

            case 'IMG':
            case 'IMGPATH':
                $path = self::getStaticPath('img');
            break;
            case 'RIMG':
            case 'RIMGPATH':
                $path = self::getResourcePath('img');
            break;

            case 'PACKAGE':
            case 'PKGPATH':
                $path = self::getStaticPath('package');
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
     * 返回Views(V)显示层的路径
     *
     * @author HanskiJay
     * @since  2020-09-10 18:49
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getViewPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = HttpManager::getCurrent('app')::getAppPath() . 'view' . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 返回Views(V)模板路径
     *
     * @author HanskiJay
     * @since  2020-09-10 18:49
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getComponentPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getViewPath('component') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取Application局部静态资源目录
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $   index 文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getStaticPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getViewPath('static') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取CSS文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    public static function getCssPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getStaticPath('css') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取JS文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    public static function getJsPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getStaticPath('js') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取IMG文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    public static function getImgPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getStaticPath('img') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }



    /**
     * 获取公共静态资源目录
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $   index 文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getResourcePath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = RESOURCE_PATH . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取公共目录下的IMG文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getPublicImgPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getResourcePath('img') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取公共目录下的JS文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getPublicJsPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getResourcePath('js') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 获取公共目录下的CSS文件目录的指定文件
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $index    文件/文件夹路径
     * @param  boolean $isExists 文件/文件夹路径是否存在
     * @return string
     */
    final public static function getPublicCssPath(string $index, &$isExists = false) : string
    {
        self::filterPath($index);
        $path     = self::getResourcePath('css') . DIRECTORY_SEPARATOR . Str::escapeSlash($index);
        $isExists = self::exists($path);
        return $path;
    }

    /**
     * 过滤路径
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string $path
     * @return void
     */
    public static function filterPath(string &$path) : void
    {
        $path = trim(trim($path, '/'), '\\');
    }

    /**
     * 判断路径是否有效
     *
     * @author HanskiJay
     * @since  2020-09-10
     * @param  string  $path
     * @return boolean
     */
    public static function exists(string $path) : bool
    {
        return is_dir($path) || is_file($path);
    }

    /**
     * 通过限制文件大小获取文件
     *
     * @author HanskiJay
     * @since  2022-08-03
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