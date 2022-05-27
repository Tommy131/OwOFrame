<?php

declare(strict_types=1);
namespace owoframe\utils;

use CurlHandle;

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
		"Connection: Keep-Alive",
		"Accept: text/html, application/xhtml+xml, */*",
		"Pragma: no-cache",
		"Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3",
		"User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)",
		'CLIENT-IP: {ip}',
		'X-FORWARDED-FOR: {ip}'
	];

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
	 * 设置HTTP Header
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $header
	 * @return Curl
	 */
	public function setHeader(array $header) : Curl
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
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
		$payload = '?';
		foreach($data as $key => $content)
		{
			$payload .= urlencode($key) . '=' . urlencode($content) . '&';
		}
		curl_setopt($this->curl, CURLOPT_URL, $this->url . $payload);
		return $this;
	}

	/**
	 * 设置Post请求的数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  array      $data
	 * @return Curl
	 */
	public function setPostData(array $data) : Curl
	{
		$payload = '';
		foreach($data as $key => $content)
		{
			$payload .= urlencode($key) . '=' . urlencode($content) . '&';
		}
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
		return $this;
	}

	/**
	 * 设置Post请求的数据
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @param  mixed
	 * @return Curl
	 */
	public function setEncPostData($post) : Curl
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
	public function setCookie(array $cookies) : Curl
	{
		$payload = '';
		foreach($cookies as $key => $cookie)
		{
			$payload .= "$key=$cookie; ";
		}
		curl_setopt($this->curl, CURLOPT_COOKIE, $payload);
		return $this;
	}

	/**
	 * 保持Cookie
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return Curl
	 */
	public function keepCookie() : Curl
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
	 * @return string
	 */
	public function getCookie()
	{
		preg_match_all('/Set-Cookie: (.*);/iU', $this->content, $cookies);
		$payload = [];
		foreach($cookies[1] as $cookie)
		{
			$key = explode('=', $cookie);
			if(isset($payload[$key[0]]) and $payload[$key[0]] !== '')
			{
				continue;
			}
			$payload[$key[0]] = $key[1];
		}
		return $payload;
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
	 * 返回CURL执行结果
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return string|boolean
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * 返回CURL是否出错的布尔值
	 *
	 * @author HanskiJay
	 * @since  2021-08-14
	 * @return boolean
	 */
	public function isError()
	{
		return curl_errno($this->curl) ? true : false;
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