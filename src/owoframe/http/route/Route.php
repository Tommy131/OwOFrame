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
 * @Date         : 2023-02-15 21:20:17
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-25 16:04:52
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http\route;



use owoframe\application\Application;
use owoframe\application\Controller;
use owoframe\event\Event;
use owoframe\event\http\PageErrorEvent;
use owoframe\http\Response;

use function owo\class_short_name;

class Route
{
    use StandardParser;

    public const TAG_STATIC_ROUTE = 'static.owo';

    /**
     * 全局路由标识
     */
    public const TAG_GLOBAL_ROUTE = '*';

    /**
     * 回调关键字
     */
    public const TAG_CALLBACK = '@callback';

    /**
     * 路由表
     *
     * @var array
     */
    protected $routingTable = [];

    /**
     * 最后一次使用的键
     *
     * @var string|null
     */
    protected $current = null;

    /**
     * 路由组设置状态
     *
     * @var boolean
     */
    protected $groupSetting = false;

    /**
     * 自动发送响应数据
     *
     * @var boolean
     */
    protected $autoSend = true;

    /**
     * 响应载体实例
     *
     * @var Response
     */
    protected $response = null;

    /**
     * 默认HTTP方法
     *
     * @var string
     */
    public static $defaultMethod = 'get';

    /**
     * 最后一次的错误信息
     *
     * @var string|null
     */
    public $lastError = null;


    /**
     * 通过指定的规则匹配路由
     *
     * @param  string      $rule
     * @param  string|null $route
     * @param  string      $range
     * @return boolean
     */
    public static function test(string $rule, ?string $route = null, string $range = '|') : bool
    {
        $route  = $route ?? \owo\get_raw_path();
        return \owo\str_is_regex($rule, $range, $rule) && (bool) preg_match_all($rule, $route);
    }

    /**
     * 通过系统自带的正则表达式匹配路由
     *
     * @param  string  $rule
     * @param  string  $route
     * @return boolean
     */
    public static function easyTest(string $rule, string $route) : bool
    {
        // 匹配规则是否采用默认表达式
        if(preg_match('/\[\w+\]{1,}/iU', $rule, $matched)) {
            $rule = RulesRegex::ALL[$matched[0]] ?? null;
            return $rule ? (bool) preg_match($rule, $route) : false;
        }
        return false;
    }

    /**
     * 通过指定的规则匹配路由
     *
     * @param  string      $rule
     * @param  string|null $route
     * @return boolean
     */
    public function match(string $rule, ?string $route = null) : bool
    {
        $route  = $route ?? $this->current();
        $result = null;
        // 匹配规则是否采用默认表达式
        if(preg_match_all('/\[\w+\]{1,}/iU', $rule, $matched))
        {
            $matched = $matched[0];
            $paths   = array_values(array_filter(explode('/', $route)));
            // 循环匹配到的默认规则
            foreach($matched as $k => $rule) {
                $rule = RulesRegex::ALL[$rule] ?? null;
                if(!$rule) continue;
                // 将匹配结果加入缓冲区
                $_      = (bool) preg_match($rule, $paths[$k]);
                $result = is_null($result) ? $_ : $result && $_;
            }
        }
        return $result ?? false;
    }


    /**
     * 通过实例化对象创建自定义路由表
     *
     * @param  array   $routingTable
     * @param  boolean $update
     */
    public function __construct(array $routingTable = [], bool $update = false)
    {
        if(empty($this->routingTable) || $update) {
            $this->routingTable = $routingTable;
        }
    }

    /**
     * 更新路由表
     *
     * @param  array $routingTable
     * @return Route
     */
    public function update(array $routingTable = []) : Route
    {
        $this->__construct($routingTable, true);
        return $this;
    }

    /**
     * 合并路由表
     *
     * @param  array $customRoutingTable
     * @return Route
     */
    public function merge(array $customRoutingTable) : Route
    {
        $this->routingTable = array_merge($this->routingTable, $customRoutingTable);
        return $this;
    }

    /**
     * 返回路由表
     *
     * @return array
     */
    public function getRoutingTable() : array
    {
        return $this->routingTable;
    }

    /**
     * 检测是否存在一个组
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasGroup(string $name) : bool
    {
        return isset($this->routingTable[$name]);
    }

    /**
     * 返回一个组
     *
     * @param  string  $name
     * @return mixed
     */
    public function getGroup(string $name)
    {
        return $this->routingTable[$name] ?? null;
    }

    /**
     * 返回最后一次使用的键
     *
     * @return string|null
     */
    public function current() : ?string
    {
        return $this->current ?? null;
    }

    /**
     * 更新最后一次使用的键
     *
     * @param  string|null $index
     * @return Route
     */
    public function index(?string $index = null) : Route
    {
        $this->current = $index;
        return $this;
    }

    /**
     * 判断是否存在一则路由
     *
     * @param  string  $route
     * @return boolean
     */
    public function exists(string $route) : bool
    {
        $group = \owo\str_split($route, 0);
        return $group ? isset($this->routingTable[$group][$route]) : false;
    }

    /**
     * 解析路由
     *
     * @param  string               $route
     * @param  callable|object|null $handler
     * @param  string               $method
     * @param  array                $varRules
     * @return Route
     */
    public function add(string $route, $handler = null, string $method = 'get', array $varRules = []) : Route
    {
        if($this->groupSetting) {
            $group    = trim($this->current(), '/\\');
            $route    = '/' . $group . $route;
            $splitted = \owo\str_split($route);
        } else {
            $splitted = \owo\str_split($route);
            $group    = $splitted[0];
        }

        if(is_string($group)) {
            $parsed =& $this->routingTable;
            $parsed =& $parsed[$group];

            $parsed[$route] =
            [
                'parsed'   => $splitted,
                'method'   => $method,
                'handler'  => $handler,
                'varRules' => $varRules
            ];
            if(!$this->groupSetting) {
                $this->index($route);
            }
        } else {
            $this->lastError = 'First level of route should be string';
        }
        return $this;
    }

    /**
     * 批量添加路由规则到组
     *
     * @param  string   $name
     * @param  callable $callback
     * @return Route
     */
    public function addGroup(string $name, callable $callback) : Route
    {
        $this->index($name);
        $this->groupSetting = true;
        $callback($this);
        $this->groupSetting = false;
        return $this;
    }

    /**
     * 添加一个组回调方法
     *
     * ! ATTENTION: 在执行路由分发 `dispatch` 方法后, 优先调用 `GroupHandler`, 其回调方法和值将会作为参数传递给真正的 `路由回调方法`!
     *
     * @param  callable    $callback
     * @param  string|null $group
     * @return Route
     */
    public function addGroupHandler(callable $callback, ?string $group = null) : Route
    {
        $group = $group ?? \owo\str_split($this->current(), 0);
        if($this->hasGroup($group)) {
            $group             =& $this->routingTable[$group];
            $group[self::TAG_CALLBACK] = $callback;
        } else {
            $this->lastError = "\$group {$group} doesn't exists";
        }
        return $this;
    }

    /**
     * 代理检测路由的有效性
     *
     * @param  string|null   $route
     * @param  callable|null $callback
     * @return boolean
     */
    protected function checkValidity(?string &$route = null, ?callable $callback = null) : bool
    {
        $route = $route ?? $this->current();
        if($this->exists($route)) {
            if(is_callable($callback))
            {
                $args = func_get_args();
                unset($args[0], $args[1]);

                $group = \owo\str_split($route, 0);
                switch(array_shift($args))
                {
                    case 'getGroupTable':
                    case 'findGroupTable':
                        $callback($this->routingTable[$group]);
                    break;

                    case 'getRouteTable':
                    case 'findRouteTable':
                        $callback($this->routingTable[$group][$route]);
                    break;

                    default:
                        // 插入组名称, 路由
                        array_unshift($args, $group, $route);
                        $callback(...$args);
                    break;
                }
            }
            return true;
        } else {
            $this->lastError = "\$route {$route} doesn't exists";
            return false;
        }
    }

    /**
     * 允许通过 [$method] 方式请求路由
     *
     * @param  string      $method
     * @param  string|null $route
     * @return Route
     */
    public function via(string $method, ?string $route = null) : Route
    {
        $this->checkValidity($route, function(array &$table) use ($method) {
            $table['method'] = $method;
        }, 'findRouteTable');
        return $this;
    }

    /**
     * 添加变量规则 (单个)
     *
     * @param  string      $var
     * @param  string      $rule
     * @param  string|null $route
     * @return Route
     */
    public function var(string $var, string $rule, ?string $route = null) : Route
    {
        $this->checkValidity($route, function(array &$table) use ($var, $rule) {
            $table['varRules'][$var] = $rule;
        }, 'findRouteTable');
        return $this;
    }

    /**
     * 添加变量规则 (多选)
     *
     * @param  string      $var
     * @param  string      $rule
     * @param  string|null $route
     * @return Route
     */
    public function vars(array $varRules, ?string $route = null) : Route
    {
        $this->checkValidity($route, function(array &$table) use ($varRules) {
            $_ =& $table['varRules'];
            $_ = array_merge($_, $varRules);
        }, 'findRouteTable');
        return $this;
    }

    /**
     * 调用处理方法/回调方法
     *
     * @access protected
     * @param  mixed $handler
     * @param  array $params
     * @return mixed
     */
    protected function handle(&$handler, array $params = [])
    {
        if(is_callable($handler) || function_exists((string) $handler)) {
            return $handler($params) ?? true;
        }
        elseif(is_string($handler))
        {
            if(is_a($handler, Controller::class, true)) {
                $handler = new $handler;
                $method  = $handler->getDefaultHandlerMethod() ?? $handler->getName();
                if(method_exists($handler, $method)) {
                    return call_user_func_array([$handler, $method], $params) ?? true;
                }
            }
            elseif(is_a($handler, Application::class, true))
            {
                $handler = new $handler;
                if(!in_array(\owo\php_current(), $handler->loadMode)) {
                    $this->lastError = "\$handler '" . $handler::getName() . "' is an Application but it disallowed to run on current PHP-Mode";
                    return false;
                }

                $controller = $handler->getDefaultController();
                if($controller instanceof Controller) {
                    $method = $controller->getDefaultHandlerMethod() ?? $controller->getName();
                    if(method_exists($controller, $method)) {
                        return call_user_func_array([$controller, $method], $params) ?? true;
                    }
                }
                elseif($handler->autoTo404Page) {
                    $event = new PageErrorEvent();
                    $event->render();
                    return $event;
                }
            }
            elseif(is_a($handler, Event::class, true)) {
                $handler = new $handler;
                $handler->trigger($params);
                return true;
            }
        }
        $this->lastError = "Invalid \$handler " . (is_object($handler) ? class_short_name($handler) : $handler);
        return false;
    }

    /**
     * 分发路由
     *
     * @param  mixed $handler
     * @param  mixed $groupHandler
     * @return mixed
     */
    public function dispatch(&$handler = null, &$groupHandler = null)
    {
        $group = $this->getGroup((string) \owo\str_split(\owo\get_raw_path(), 0)) ?? [];
        // 开始组回调
        $groupHandler  = $group[self::TAG_CALLBACK] ?? null;
        $_groupHandler = $this->handle($groupHandler);

        $paths = \owo\get_path(0);
        if(empty($paths) || empty($group)) {
            // ~ 当不存在有效的应用程序时, 加载默认的全局路由规则
            if(!$this->run(['msg' => 'No Application Founded!'], 404)) {
                $group = $this->getGroup(self::TAG_GLOBAL_ROUTE) ?? [];
            }
        }

        // 初始化需要的变量
        $params = [];

        // 开始路由组循环匹配
        foreach($group as $route => $data) {
            if($route === self::TAG_CALLBACK) {
                continue;
            }

            // 开始路由匹配
            $parsed = $data['parsed'] ?? \owo\str_split($route);
            foreach($paths as $k => $p)
            {
                $isFounded = null;
                // 创建一个用于设置状态的匿名函数
                $status = function(bool $_) use (&$isFounded) {
                    $isFounded = is_null($isFounded) ? $_ : $isFounded && $_;
                };

                $rule = $parsed[$k] ?? '';
                if(($rule === self::TAG_GLOBAL_ROUTE) || ($p === $rule) || $this->getAlias($p) || self::test($rule, $p) || self::easyTest($rule, $p)) {
                    $status(true);
                }
                elseif(\owo\stri_has($rule, '$')) {
                    // 将路由中的值作为变量传递给参数
                    $var  = str_replace('$', '', $rule);
                    $rule = $data['varRules'][$rule] ?? null;
                    if($rule && \owo\str_is_regex($rule, '|', $rule)) {
                        if(!preg_match($rule, $var)) {
                            $status(false);
                            $this->lastError = "\$var {$var} rule match failed";
                            continue;
                        }
                    }
                    $params[$var] = $p;
                    $status(true);
                } else {
                    $status(false);
                }
            }

            // 若当前路由成功匹配则执行下方代码
            if($isFounded)
            {
                $method = $data['method'] ?? static::$defaultMethod;
                if(($method !== 'any') && (strtolower(\owo\server('REQUEST_METHOD')) !== $method)) {
                    $this->lastError = 'Illegal request method on this route';
                    return false;
                }

                // 开始路由回调
                $handler = $data['handler'] ?? null;
                $this->response = new Response;
                $this->response->setResponseCode(200)->setPrepareSendData($this->handle($handler,
                [
                    'parameters'   => $params,
                    'response'     => $this->response,
                    'groupHandler' => $groupHandler,
                    'result'       => $_groupHandler
                ]));

                return $this->autoSend ? $this->response->send() : true;
            }
        }
        $this->run(['msg' => 'No Application Founded'], 404);
        return false;
    }


    /**
     *  添加任意路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function any(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'any', $varRules);
    }

    /**
     *  添加 GET 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function get(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'get', $varRules);
    }

    /**
     *  添加 HEAD 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function head(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'head', $varRules);
    }

    /**
     *  添加 POST 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function post(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'post', $varRules);
    }

    /**
     *  添加 PUT 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function put(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'put', $varRules);
    }

    /**
     *  添加 DELETE 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function delete(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'delete', $varRules);
    }

    /**
     *  添加 OPTIONS 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function options(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'options', $varRules);
    }

    /**
     *  添加 PATCH 路由
     *
     * @param  string              $route
     * @param  callable|object|null $handler
     * @param  array           $varRules
     * @return Route
     */
    public function patch(string $route, $handler = null, array $varRules = []) : Route
    {
        return $this->add($route, $handler, 'patch', $varRules);
    }
}
?>