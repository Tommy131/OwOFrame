<?php

declare(strict_types=1);
namespace owoframe\utils;

use CurlHandle;
use owoframe\exception\OwOFrameException;
use owoframe\helper\Helper;

class Curl
{
	/**
	 * cURL
	 *
	 * @access protected
	 * @var CurlHandle
	 */
	protected $curl;

	/**
	 * 请求URL地址
	 *
	 * @access protected
	 * @var string
	 */
	protected $url;

	/**
	 * 返回头部信息的变量
	 *
	 * @var boolean
	 */
	protected $returnHeader;

	/**
	 * 获取到的上下文
	 *
	 * @access protected
	 * @var string
	 */
	protected $content;

	/**
	 * HTTP请求头
	 *
	 * @var array
	 */
	public static $defaultHeader =
	[
		'Connection: Keep-Alive',
		'Accept: text/html, application/xhtml+xml, */*',
		'Pragma: no-cache',
		'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
		'User-Agent: {userAgent}',
		'CLIENT-IP: {ip}',
		'X-FORWARDED-FOR: {ip}'
	];

	public $header = [];

	/**
	 * IP组
	 *
	 * @var array
	 */
	public static $ip_long =
	[
		['607649792', '608174079'],     //36.56.0.0   - 36.63.255.255
		['1038614528', '1039007743'],   //61.232.0.0  - 61.237.255.255
		['1783627776', '1784676351'],   //106.80.0.0  - 106.95.255.255
		['2035023872', '2035154943'],   //121.76.0.0  - 121.77.255.255
		['2078801920', '2079064063'],   //123.232.0.0 - 123.235.255.255
		['-1950089216', '-1948778497'], //139.196.0.0 - 139.215.255.255
		['-1425539072', '-1425014785'], //171.8.0.0   - 171.15.255.255
		['-1236271104', '-1235419137'], //182.80.0.0  - 182.92.255.255
		['-770113536', '-768606209'],   //210.25.0.0  - 210.47.255.255
		['-569376768', '-564133889'],   //222.16.0.0  - 222.95.255.255
	];



	/**
	 * 初始化CURL
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 */
	public function __construct()
	{
		if($this->curl instanceof CurlHandle) {
			curl_close($this->curl);
		}
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$this->returnHeader(true);
		$this->setTimeout(10);
	}

	/**
	 * 执行CURL请求
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return Curl
	 */
	public function exec() : Curl
	{
		$this->content = curl_exec($this->curl);
		return $this;
	}

	/**
	 * 返回CURL资源
	 *
	 * @author HanskiJay
	 * @since  2022-02-27
	 * @return resource|null
	 */
	public function getResource()
	{
		return $this->curl ?? null;
	}

	/**
	 * 设置代理服务器
	 *
	 * @author HanskiJay
	 * @since  2022-07-24
	 * @param  string  $address
	 * @param  integer $port
	 * @return Curl
	 */
	public function useProxy(string $address, int $port) : Curl
	{
		if(!Helper::isIp($address) && !Helper::isDomain($address)) {
			throw new OwOFrameException('无效的代理地址!');
		}
		curl_setopt($this->curl, CURLOPT_PROXY, $address);
		curl_setopt($this->curl, CURLOPT_PROXYPORT, $port);
		return $this;
	}

	/**
	 * 设置UA (User Agent)
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string      $ua
	 * @return Curl
	 */
	public function setUA(string $ua) : Curl
	{
		curl_setopt($this->curl, CURLOPT_USERAGENT, $ua);
		return $this;
	}

	/**
	 * 将UA设置为移动端 (iPhone 12 Pro)
	 *
	 * @author HanskiJay
	 * @since  2022-07-24
	 * @return Curl
	 */
	public function userAgentInMobile() : Curl
	{
		return $this->setUA('Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1 Edg/103.0.5060.114');
	}

	/**
	 * 将UA设置为PC端 (Edge)
	 *
	 * @author HanskiJay
	 * @since  2022-07-24
	 * @return Curl
	 */
	public function userAgentInPC() : Curl
	{
		return $this->setUA('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36 Edg/103.0.1264.62');
	}

	/**
	 * 设置请求地址
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string      $url
	 * @return Curl
	 */
	public function setUrl($url) : Curl
	{
		$this->url = $url;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		return $this;
	}

	/**
	 * 返回设置的请求地址
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return string
	 */
	public function getUrl() : string
	{
		return $this->url;
	}

	/**
	 * 设置HTTP Header
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $header
	 * @return Curl
	 */
	public function setHeader(array $header) : Curl
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array_merge($header, $this->header));
		return $this;
	}

	/**
	 * 设置来源
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string      $referer
	 * @return Curl
	 */
	public function setReferer(string $referer) : Curl
	{
		curl_setopt($this->curl, CURLOPT_REFERER, $referer);
		return $this;
	}

	/**
	 * 设置Get请求的数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $data
	 * @return Curl
	 */
	public function setGetData(array $data) : Curl
	{
		curl_setopt($this->curl, CURLOPT_URL, $this->url . http_build_query($data));
		return $this;
	}

	/**
	 * 设置Post请求的数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $data
	 * @param  boolean    $withJson
	 * @return Curl
	 */
	public function setPostData(array $data, bool $withJson = false) : Curl
	{
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $withJson ? json_encode($data) : http_build_query($data));
		return $this;
	}

	/**
	 * 设置原始Post请求的数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string      $post
	 * @return Curl
	 */
	public function setPostDataRaw(string $post) : Curl
	{
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
		return $this;
	}

	/**
	 * 设置CURL的请求超时时间
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  int      $timeout
	 * @return Curl
	 */
	public function setTimeout(int $timeout) : Curl
	{
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
		return $this;
	}

	/**
	 * 设置CURL参数
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  int        $option
	 * @param  mixed      $value
	 * @return Curl
	 */
	public function setOpt(int $option, $value) : Curl
	{
		curl_setopt($this->curl, $option, $value);
		return $this;
	}

	/**
	 * 设置是否返回HTTP请求头数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  boolean      $bool
	 * @return Curl
	 */
	public function returnHeader(bool $bool) : Curl
	{
		$this->returnHeader = $bool;
		curl_setopt($this->curl, CURLOPT_HEADER, $bool);
		return $this;
	}

	/**
	 * 设置是否返回Body
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  boolean      $bool
	 * @return Curl
	 */
	public function returnBody(bool $bool) : Curl
	{
		curl_setopt($this->curl, CURLOPT_NOBODY, !$bool);
		return $this;
	}

	/**
	 * 设置Cookie
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $cookies
	 * @return Curl
	 */
	public function setCookies(array $cookies) : Curl
	{
		$payload = '';
		foreach($cookies as $key => $cookie) {
			$payload .= "$key=$cookie; ";
		}
		curl_setopt($this->curl, CURLOPT_COOKIE, $payload);
		return $this;
	}

	/**
	 * 设置原始Cookies数据
	 *
	 * @param  string $cookies
	 * @return Curl
	 */
	public function setCookiesInRaw(string $cookies) : Curl
	{
		curl_setopt($this->curl, CURLOPT_COOKIE, $cookies);
		return $this;
	}

	/**
	 * 清空Cookie
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return Curl
	 */
	public function clearCookie() : Curl
	{
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, '');
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, '');
		return $this;
	}

	/**
	 * 返回Cookie
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return array
	 */
	public function getCookies() : array
	{
		$content = $this->getContent($headers);
		$content = !$this->returnHeader ? $content : $headers;
		preg_match_all('/Set-Cookie: (.*);/iU', $content, $cookies);
		$payload = [];
		foreach($cookies[1] as $cookie) {
			$key = explode('=', $cookie);
			if(isset($payload[$key[0]]) and $payload[$key[0]] !== '') {
				continue;
			}
			$payload[$key[0]] = $key[1];
		}
		return $payload;
	}

	/**
	 * 返回CURL执行结果
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  string &$headers
	 * @return string
	 */
	public function getContent(&$headers = '') : string
	{
		$_ = $this->content;
		if($_ === false) {
			return '';
		}
		if($this->returnHeader) {
			$headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
			$headers    = substr($_, 0, $headerSize);
			$_          = substr($_, $headerSize);
		}
		return $_;
	}

	/**
	 * 以Json格式解码
	 *
	 * @author HanskiJay
	 * @since  2022-07-28
	 * @return array|object|null
	 */
	public function decodeWithJson(bool $toObject = true)
	{
		return json_decode($this->getContent(), !$toObject);
	}

	/**
	 * 返回CURL是否出错的布尔值
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return int
	 */
	public function isError() : int
	{
		return curl_errno($this->curl);
	}

	/**
	 * 返回错误信息
	 *
	 * @author HanskiJay
	 * @since  2022-07-17
	 * @return string|null
	 */
	public function getError() : ?string
	{
		return curl_error($this->curl) ?? null;
	}

	/**
	 * 返回随机IP地址
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return string
	 */
	public static function getRadomIp()
	{
		$rand_key = mt_rand(0, 9);
		return long2ip(mt_rand((int) static::$ip_long[$rand_key][0], (int) static::$ip_long[$rand_key][1]));
	}
}