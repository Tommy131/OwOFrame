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

use owoframe\application\AppManager;
use owoframe\exception\DomainRuleException;
use owoframe\helper\Helper;

class DomainRule
{
	/**
	 * 绑定到URL标签
	 */
	public const TAG_BIND_TO_URL = 0;

	/**
	 * 绑定到应用程序标签
	 */
	public const TAG_BIND_TO_APPLICATION = 1;

	/**
	 * 绑定域名组
	 *
	 * @var array
	 */
	private static $bind = [];


	/**
	 * 绑定域名到绑定类型
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string  $domain
	 * @param  int     $bindType
	 * @param  string  $to
	 * @return boolean
	 */
	public static function bind(string $domain, int $bindType, string $to) : bool
	{
		if(self::isBound($domain)) {
			throw new DomainRuleException("Domain {$domain} has already bound with " . static::$bind[$domain] . '!');
		}
		if(!Helper::isDomain($domain)) {
			throw new DomainRuleException('First argument must be a domain!');
		}

		switch($bindType) {
			// Bind Domain to URL;
			// !Attention: After binding, this domain name will not be able to access other pages!!!
			// !注意: 绑定后此域名将不能访问其他页面!!!
			case self::TAG_BIND_TO_URL:
				$to = str_replace('{domain}', $domain, $to);
				if(!Helper::isUrl($to)) {
					throw new DomainRuleException('Second argument must be an URL!');
				}
			break;

			case self::TAG_BIND_TO_APPLICATION:
				if(!AppManager::getApp($to)) {
					throw new DomainRuleException('Invalid Application Name!');
				}
			break;
		}
		static::$bind[$domain] = [$bindType, $to];
		return true;
	}

	/**
	 * 获取绑定数据
	 *
	 * @author HanskiJay
	 * @since  2021-11-03
	 * @param  string      $domain
	 * @param  &$bindType
	 * @return string|null
	 */
	public static function get(string $domain, &$bindType = null) : ?string
	{
		if(!self::isBound($domain)) {
			return null;
		}
		$bindType = static::$bind[$domain][0];
		return static::$bind[$domain][1];
	}

	/**
	 * 返回查找的域名是否已绑定
	 *
	 * @author HanskiJay
	 * @since  2021-11-02
	 * @param  string  $domain
	 * @return boolean
	 */
	public static function isBound(string $domain) : bool
	{
		return isset(static::$bind[$domain]);
	}
}