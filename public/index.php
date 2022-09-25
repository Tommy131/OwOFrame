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

use owoframe\System;
use owoframe\http\HttpManager;

require_once('../src/bootstrap.php');
System::init($classLoader);

$parser = HttpManager::getParameters(0);
$mode   = array_shift($parser);

if($mode === 'static.owo')
{
    if(count($parser) === 2) {
        $type    = array_shift($parser);
        $hashTag = @array_shift(explode('.', array_shift($parser)));
        $file    = F_CACHE_PATH . $type . DIRECTORY_SEPARATOR . $hashTag . '.php';
        if(is_file($file)) {
            $tempData = require_once($file);
            if(is_array($tempData)) {
                if(isset($tempData['expireTime'])) {
                    if(microtime(true) - $tempData['expireTime'] <= 0) {
                        $reason = 'FILE ALLOWED ACCESS TIME HAS EXPIRED';
                        $logger->info('[403@Access Denied=' . $reason . '] ' . System::getClientIp() . ' -> ' . HttpManager::getCompleteUrl());
                        stdDie($reason, '', 403);
                    }
                }
                if(isset($tempData['callback']) && is_callable($tempData['callback'])) {
                    $tempData['callback']();
                }
            }
        } else {
            $logger->info('[404@Not Found] ' . System::getClientIp() . ' -> ' . HttpManager::getCompleteUrl());
            stdDie('REQUESTED FILE NOT FOUND', '<p>FILE: ' . $hashTag . '.' . $type . '</p>');
        }
    } else {
        $logger->info('[403@Access Denied] ' . System::getClientIp() . ' -> ' . HttpManager::getCompleteUrl());
        stdDie('', '', 403);
    }
} else {
    HttpManager::start();
}



/**
 * 标准结束脚本输出
 *
 * @author HanskiJay
 * @since  2021-03-14
 * @param  string      $title     输出标题(为空则不输出任何结果)
 * @param  string      $customMsg 自定义输出信息
 * @param  int|integer $code      HTTP响应状态码
 * @return void
 */
function stdDie(string $title, string $customMsg = '', int $code = 404) : void
{
    HttpManager::setStatusCode($code);
    if(($title !== '') && ($title !== null)) {
        die(
            '<div style="weight: 100%; text-align: center">'.
                '<p>[HTTP_ERROR@' . $code . ']</p>'.
                '<p style="font-weight: 600">---------- ' . $title . ' ----------</p>'.
                $customMsg .
                '<p>CLIENT:     ' . System::getClientIp() . '</p>'.
                '<p>User-Agent: ' . server('HTTP_USER_AGENT') . '</p>'.
                '<p style="font-weight: 600">---------------------------------------------------------</p>'.
                '<p>[' . date('Y-m-d H:i:s') . ']</p>'.
            '</div>'
        );
    }
}