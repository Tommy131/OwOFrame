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
namespace owoframe\http\route;

use owoframe\helper\Helper;
use owoframe\application\ApiProcessor;
use owoframe\application\AppManager;

class RouteRule
{
	// TODO: 支持路由分组管理;

	/* @array 路由映射表 */
	private static $routeRule = [];
	/* @array 域名绑定表 */
	private static $domainRule = [];
	/* @array API处理器绑定池 */
	private static $apiRule = [];

	/**
	 * @method      bind
	 * @description 绑定路由规则到控制器
	 * @author      HanskiJay
	 * @doenIn      2021-02-07
	 * @param       string[app_controllerName|Usage: -> AppName@ControllerName]
	 * @param       string[rule|绑定规则]
	 * @return      void
	 */
	public static function bind(string $app_controllerName, string $rule) : void
	{

	}

	/**
	 * @method      getNormalRule
	 * @description 从规则表中获取一个已有的路由绑定
	 * @param       string[index|路由规则]
	 * @return      null or ControllerBase or Closure
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getNormalRule(string $index)
	{
		return self::$routeRule[$index] ?? null;
	}

	/**
	 * @method      getNormalRules
	 * @description 返回路由规则表
	 * @return      array
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getNormalRules() : array
	{
		return self::$routeRule;
	}

	/**
	 * @method      compareDomain
	 * @description 比对域名是否相同
	 * @author      HanskiJay
	 * @doenIn      2021-01-16
	 * @param       string[domain1|第一个域名]
	 * @param       string[domain2|第二个域名]
	 * @return      boolean
	 */
	public static function compareDomain(string $domain1, string $domain2) : bool
	{
		// return (bool) (preg_match("/{$domain1}/i", $domain2) || preg_match("/{$domain2}/i", $domain1));
		if(stripos($domain1, '*') !== false) {
			$domain1 = explode('.', $domain1);
			array_shift($domain1);
			$domain1 = implode('.', $domain1);
		}
		if(stripos($domain2, '*') !== false) {
			$domain2 = explode('.', $domain2);
			array_shift($domain);
			$domain2 = implode('.', $domain2);
		}
		return $domain1 === $domain2;
	}

	/**
	 * @method      domain
	 * @description 绑定域名到应用程序 | Bind domains to Application
	 * @author      HanskiJay
	 * @doenIn      2021-01-16
	 * @param       string[domain|必须是泛域名]
	 * @param       array or string[args|传入的参数, 可以仅是AppName, 也可以是数组 [二级域名=>AppName]]
	 * @return      void
	 */
	public static function domain(string $domain, ...$args) : void
	{
		if(!Helper::isDomain($domain)) {
			throw error('Method '.__CLASS__.'::domain parameter 1 expected string domain, but there was incorrect domain given.');
		}

		if(count($args) <= 0) {
			throw error('Method '.__CLASS__.'::domain parameter 2 expected an application name or a prefix domain name, but nothing is given.');
		}

		if(is_string($args[0])) {
			if(!AppManager::hasApp($args[0])) {
				throw error("Cannot find application '{$args[0]}'!");
			}
			self::$domainRule[$domain] = $args[0];
		}

		if(is_array($args[0])) {
			foreach($args[0] as $prefix => $appName) {
				if(!AppManager::hasApp($appName)) {
					throw error("Cannot find application '{$appName}'!");
				}
				self::$domainRule[$prefix.'.'.$domain] = $appName;
			}
		}
	}

	/**
	 * @method      getDomainBind
	 * @description 获取指定的域名绑定规则
	 * @author      HanskiJay
	 * @doenIn      2021-01-16
	 * @param       string[domain|指定的域名]
	 * @return      null or string
	 */
	public static function getDomainBind(string $domain) : ?string
	{
		return self::$domainRule[$domain] ?? null;
	}

	/**
	 * @method      getDomainRule
	 * @description 返回域名绑定表
	 * @return      array
	 * @author      HanskiJay
	 * @doneIn      2020-09-09 18:03
	*/
	public static function getDomainRules() : array
	{
		return self::$domainRule;
	}

	/**
	 * @method      bindApiProcessor
	 * @description 绑定API处理器
	 * @author      HanskiJay
	 * @doenIn      2021-02-04
	 * @param       string|class@ApiProcessor[api|绑定的实例对象]
	 * @return      void
	 */
	public static function bindApiProcessor($api) : void
	{
		if(is_string($api)) {
			$ref = new \ReflectionClass($api);
			if(($ref = $ref->getParentClass()) !== false) {
				if($ref->getName() === ApiProcessor::class) {
					$api = new $api;
				}
			}
		}
		if(!$api instanceof ApiProcessor) return;
		if(is_null(self::getApiProcessor($api->getName()))) {
			$api->setPathParam(Router::getParameters(2));
			self::$apiRule[$api->getName()] = $api;
		}
	}

	/**
	 * @method      getApiProcessor
	 * @description 返回一个有效的api处理器
	 * @author      HanskiJay
	 * @doenIn      2021-02-04
	 * @return      array
	 */
	public static function getApiProcessor(string $apiName) : ?ApiProcessor
	{
		return self::$apiRule[$apiName] ?? null;
	}

	/**
	 * @method      getApiProcessors
	 * @description 返回所有的api处理器
	 * @author      HanskiJay
	 * @doenIn      2021-02-04
	 * @return      array
	 */
	public static function getApiProcessors() : array
	{
		return self::$apiRule;
	}
}