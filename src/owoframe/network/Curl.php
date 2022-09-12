<?php

declare(strict_types=1);
namespace owoframe\network;

use CurlHandle;

use owoframe\utils\Str;
use owoframe\exception\OwOFrameException;

class Curl
{
    /**
     * UserAgent in iPhone 12 Pro (Mobile)
     */
    public const UA_MOBILE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1 Edg/103.0.5060.114';

    /**
     * UserAgent in Edge (PC)
     */
    public const UA_PC = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36 Edg/103.0.1264.62';

    public const DEFAULT_HEADER =
    [
        'Content-Type'    => 'application/x-www-form-urlencoded; charset=UTF-8',
        'Accept'          => '*/*',
        'Accept-Language' => 'zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
        'User-Agent'      => self::UA_PC,
        'Connection'      => 'Keep-Alive',
        'Pragma'          => 'no-cache',
    ];

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
     * 原始请求头
     *
     * @access protected
     * @var array
     */
    protected $headers = [];

    /**
     * 格式化后的请求头
     *
     * @access protected
     * @var array
     */
    protected $formattedHeaders = [];

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
     * @return void
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
     * @return Curl
     */
    public function exec() : Curl
    {
        foreach($this->headers as $index => $value) {
            $this->formattedHeaders[] = $index . ': ' . $value;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->formattedHeaders);

        $this->content = curl_exec($this->curl);
        return $this;
    }

    /**
     * 返回CURL资源
     *
     * @return resource|null
     */
    public function getResource()
    {
        return $this->curl ?? null;
    }

    /**
     * 使用随机IP地址
     *
     * @return Curl
     */
    public function useRadomIp() : Curl
    {
        $radomIp = Network::getRadomIp();
        return $this->setHeaders([
            'CLIENT-IP'       => $radomIp,
            'X-FORWARDED-FOR' => $radomIp
        ]);
    }

    /**
     * 设置代理服务器
     *
     * @param  string  $address
     * @param  integer $port
     * @return Curl
     */
    public function useProxy(string $address, int $port) : Curl
    {
        if(!Network::isIp($address) && !Str::isDomain($address)) {
            throw new OwOFrameException('无效的代理地址!');
        }
        curl_setopt($this->curl, CURLOPT_PROXY, $address);
        curl_setopt($this->curl, CURLOPT_PROXYPORT, $port);
        return $this;
    }

    /**
     * 设置UA (User Agent)
     *
     * @param  string $ua
     * @return Curl
     */
    public function setUA(string $ua) : Curl
    {
        curl_setopt($this->curl, CURLOPT_USERAGENT, $ua);
        ini_set('user_agent', $ua);
        return $this;
    }

    /**
     * 将UA设置为移动端 (iPhone 12 Pro)
     *
     * @return Curl
     */
    public function userAgentInMobile() : Curl
    {
        return $this->setUA(self::UA_MOBILE);
    }

    /**
     * 将UA设置为PC端 (Edge)
     *
     * @return Curl
     */
    public function userAgentInPC() : Curl
    {
        return $this->setUA(self::UA_PC);
    }

    /**
     * 设置请求地址
     *
     * @param  string $url
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
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * 设置Header的单个参数
     *
     * @author HanskiJay
     * @since  2021-09-09
     * @param  string $index
     * @param  string $value
     * @return Curl
     */
    public function setHeader(string $index, string $value) : Curl
    {
        $this->headers[$index] = $value;
        return $this;
    }

    /**
     * 设置HTTP Header
     *
     * @param  array $headers
     * @return Curl
     */
    public function setHeaders(array $headers) : Curl
    {
        foreach($headers as $index => $value) {
            if(!is_string($index)) continue;
            $this->headers[$index] = $value;
        }
        return $this;
    }

    public function setContentType(string $type) : Curl
    {
        return $this->setHeader('Content-Type', $type);
    }

    /**
     * 设置来源
     *
     * @param  string $referer
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
     * @param  array $data
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
     * @param  array   $data
     * @param  boolean $withJson
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
     * @param  string $post
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
     * @param  int $timeout
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
     * @param  int   $option
     * @param  mixed $value
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
     * @param  boolean $bool
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
     * @param  boolean $bool
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
     * @param  array $cookies
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
     * @return array
     */
    public function getCookies() : array
    {
        $payload = [];
        $content = $this->getContent($headers);
        $content = (!$this->returnHeader ? $content : $headers) ?? '';

        if(preg_match_all('/Set-Cookie: (.*);/iU', $content, $cookies)) {
            foreach($cookies[1] as $cookie) {
                $key = explode('=', $cookie);
                if(isset($payload[$key[0]]) and $payload[$key[0]] !== '') {
                    continue;
                }
                $payload[$key[0]] = $key[1];
            }
        }
        return $payload;
    }

    /**
     * 返回CURL执行结果
     *
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
     * 检测是否为移动设备访问
     *
     * @author HanskiJay
     * @since  2021-01-10
     * @return boolean
     */
    public static function isMobile(&$agent = null) : bool
    {
        //获取USER AGENT
        $agent = server('HTTP_USER_AGENT');

        return (bool) preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $agent);
    }
}