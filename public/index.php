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
 * @Date         : 2023-02-01 19:53:21
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-25 15:53:17
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);

require_once('../src/script/bootstrap.php');
require_once('../src/script/systemFunctions.php');
require_once('../src/script/httpFunctions.php');

use owoframe\application\standard\DefaultApp;
use owoframe\System;
use owoframe\http\route\Route;
use owoframe\http\route\RulesRegex;

System::init();
$finalRoute = new Route;

// 添加全局静态路由
$finalRoute->get('/' . Route::TAG_STATIC_ROUTE . '/$type/$hashTag', function(array $obj)
{
    $obj      = (object) $obj;
    $params   = $obj->parameters;
    $response = $obj->response;
    // 解压参数
    extract($params);
    $file = owo\cache_path("{$type}/{$hashTag}.php");
    if(empty($params) || !isset($type, $hashTag) || !file_exists($file)) {
        $response->setResponseCode(403);
        return ['msg' => 'Access Denied'];
    }
    $output = require_once($file);
    if(is_string($output)) {
        $response->setContentType('txt')->setResponseCode(200);
        return $output;
    }
    elseif(is_array($output)) {
        $expireTime = $output['expireTime'] ?? 0;
        if(microtime(true) - $expireTime <= 0) {
            $response->setResponseCode(403);
            return ['msg' => 'FILE ALLOWED ACCESS TIME HAS EXPIRED'];
        }
        $response->mergeHeader($output['header'] ?? []);
        $response->setResponseCode(200);
        return file_get_contents($output['filePath']);
    }
    return $output;
})->vars([
    'type'    => RulesRegex::ONLY_LOWERCASE_LETTERS,
    'hashTag' => RulesRegex::ONLY_MIXED_LETTERS_AND_NUMBERS
])->get('/*', DefaultApp::class);

// 寻找自定义路由
$routeFile = owo\_global('system.customRouteFileName', 'route_10380WH9DH520495AD3347M32126');
$routeFile = "./{$routeFile}.php";
if(is_file($routeFile)) {
    $customRoute = require_once($routeFile);
    if($customRoute instanceof Route) {
        $finalRoute->merge($customRoute->getRoutingTable());
        $finalRoute->mergeAlias($customRoute->getAliases());
    }
}

$finalRoute->dispatch();
?>