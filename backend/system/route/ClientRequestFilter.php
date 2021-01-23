<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com

************************************************************************/

declare(strict_types=1);
namespace backend\system\route;

use backend\OwOFrame;
use backend\system\utils\Config;
use backend\system\utils\Logger;
use backend\system\exception\RouterException;
use backend\system\exception\OwOFrameException;

class ClientRequestFilter
{
	/* @string const 日志识别名称 */
	public const PREFIX = 'CRF/BeforeRoute';

	/* @CRF 本类实例对象 */
	private static $instance = null;
	/* @Config 封禁列表配置文件实例 */
	private static $banned = null;
	/* @Config 黑名单配置文件实例 */
	private static $blacklist = null;
	/* @string 当前客户端地址 */
	public static $currentIp = '';
	/* @string 当前客户端地址(转换过后) */
	public static $currentIpC = '';
	/* @int 限定请求频率(当客户端处于暂时封禁状态时) */
	public static $maxFrequency = 10;
	/* @int 限定最大请求频率(当客户端处于暂时封禁状态时) */
	public static $maxFrequencyTop = 20;
	/* @int 默认封禁天数(当客户端请求频率高于限定值时) */
	public static $bannedDays = 1;


	public function __construct()
	{
		if(self::$instance instanceof ClientRequestFilter) {
			throw new OwOFrameException('Class '.get_class($this).' was Initialized!');
		}
		self::$instance   = $this;
		self::$banned     = new Config(TMP_PATH . 'banned.json', Config::JSON);
		self::$blacklist  = new Config(TMP_PATH . 'blacklist.json', Config::JSON);
		self::$currentIp  = OwOFrame::getClientIp();
		self::$currentIpC = str_replace('.', '-', self::$currentIp);
		if(in_array(self::$currentIp, self::$blacklist->getAll())) {
			Logger::writeLog('[403] Client '.self::$currentIp.'\'s IP is in the blacklist, request deined.', self::PREFIX);
			OwOFrame::setStatus(403);
			exit;
		}
		Logger::writeLog('[200] Client '.self::$currentIp. ' requested url ['.Router::getCompleteUrl().']', self::PREFIX);
	}

	/**
	 * @method      checkValid
	 * @description 检查当前请求源的有效性
	 * @description Check the validity of the current request source
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function checkValid() : void
	{
		if($banned = self::$banned->get(self::$currentIpC)) {
			if($banned['frequency'] >= self::$maxFrequencyTop) {
				$banned['ipBlockedToTime'] = microtime(true) + self::$bannedDays * 24 * 3600;
				self::$banned->set(self::$currentIpC, $banned);
				self::$banned->save();
			}
			elseif(microtime(true) - $banned['ipBlockedToTime'] >= 0) {
				self::unbanClient();
			}
		}
		if($c = self::$banned->get(self::$currentIpC.'-CliBanned')) {
			if(microtime(true) - $c['ipBlockedToTime'] >= 0) {
				self::unblockClientFromCLI();
			}
		}
		if($this->isMaxRequestFrequency() && isset($banned) && ($banned['frequency'] >= self::$maxFrequencyTop) || self::isClientBannedFromCLI()) {
			self::clientRequested();
			Logger::writeLog('[403] Client '.self::$currentIp.'\'s IP was banned from OwOWebServer, request deined.', self::PREFIX);
			echo "当前客户端已被系统阻止进一步操作, 请在 ".self::getUnbanTime()." 后重试你的请求.";
			OwOFrame::setStatus(403);
			exit;
		}
	}


	/**
	 * @method      getUnbanTime
	 * @description 获取客户端的解除封禁时间
	 * @return      string/Date
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function getUnbanTime() : string
	{
		if(!self::isBanned()) {
			return 'null';
		}
		$c = self::$banned->get(self::$currentIpC.'-CliBanned');
		$c = $c ? $c : self::$banned->get(self::$currentIpC);
		$c = $c ? $c : [];
		return !empty($c) ? date("Y-m-d H:i:s", (int) $c['ipBlockedToTime']) : 'null';
	}

	/**
	 * @method      isBanned
	 * @description 判断当前客户端是否被封禁
	 * @description Determine whether the current client is blocked
	 * @param       bool[checkCLI|开启CLI服务端封禁检测模式(Default:false)]
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function isBanned(bool $checkCLI = false) : bool
	{
		if($checkCLI && $this->isClientBannedFromCLI()) {
			return true;
		}
		return self::$banned->exists(self::$currentIpC);
	}

	/**
	 * @method      banClient
	 * @description 封禁当前客户端
	 * @description Ban the client currently
	 * @param       int[blockTo|封禁到多少分钟后(Default:10)]
	 * @param       bool[force|强制永久封禁(Default:false)]
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function banClient(int $blockTo = 10, bool $force = false) : void
	{
		if($this->isBanned()) {
			$this->clientRequested();
			return;
		}
		$untilTo = !$force ? (microtime(true) + ($blockTo * 60)) : 999999999;
		self::$banned->set(self::$currentIpC, 
		[
			'ipBlockedToTime' => $untilTo,
			'frequency' => 1
		]);
		self::$banned->save();
	}

	/**
	 * @method      clientRequested
	 * @description 当客户端发起了请求时执行该方法
	 * @description This method is executed when the client initiates a request
	 * @return      int
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function clientRequested() : int
	{
		if(!$this->isBanned()) {
			$this->banClient();
			return 1;
		}
		$count = self::$banned->getNested(self::$currentIpC.'.frequency');
		self::$banned->setNested(self::$currentIpC.'.frequency', ++$count);
		self::$banned->save();
		return $count;
	}

	/**
	 * @method      getRequestFrequency
	 * @description 获取客户端的请求频率
	 * @description Get the request frequency of the client
	 * @return      int
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function getRequestFrequency() : int
	{
		if(!$this->isBanned()) {
			return 0;
		}
		return self::$banned->getNested(self::$currentIpC.'.frequency') ?? 0;
	}

	/**
	 * @method      isMaxRequestFrequency
	 * @description 判断客户端的请求频率是否达到最大设定值
	 * @description Determine whether the client's request frequency reaches the maximum set value
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function isMaxRequestFrequency() : bool
	{
		return $this->getRequestFrequency() >= self::$maxFrequency;
	}

	/**
	 * @method      unbanClient
	 * @description 解除当前对客户端的普通常规封禁
	 * @description Lift the current general ban on the client
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function unbanClient() : void
	{
		if(self::$banned->exists(self::$currentIpC)) {
			self::$banned->remove(self::$currentIpC);
			self::$banned->save();
		}
	}

	/**
	 * @method      blockClientFromCLI
	 * @description 当数据处理模型检测到来自CLI服务端的IP封禁之后, 调用此方法防止客户端继续发送数据请求CLI服务端
	 * @description When the data processing model detects the IP ban from the CLI server, this method is called to prevent the client from continuing to send data to request the CLI server
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function blockClientFromCLI() : void
	{
		self::$banned->set(self::$currentIpC.'-CliBanned', 
		[
			'isClientBannedFromCLI' => true,
			'ipBlockedToTime'       => microtime(true) + 600
		]);
		self::$banned->save();
	}

	/**
	 * @method      isClientBannedFromCLI
	 * @description 判断当前客户端是否被CLI服务器所封禁
	 * @return      boolean
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function isClientBannedFromCLI() : bool
	{
		return self::$banned->exists(self::$currentIpC.'-CliBanned');
	}

	/**
	 * @method      unblockClientFromCLI
	 * @description 解除CLI对客户端的封禁(仅限WebServer单方面解除, 不会影响CLI服务端的封禁策略)
	 * @return      void
	 * @author      HanskiJay
	 * @doneIn      2020-10-16 17:06
	*/
	public function unblockClientFromCLI() : void
	{
		if(self::$banned->exists(self::$currentIp.'-CliBanned')) {
			self::$banned->remove(self::$currentIpC.'-CliBanned');
			self::$banned->save();
		}
	}
}
?>