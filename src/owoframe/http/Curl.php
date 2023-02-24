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
 * @Date         : 2023-02-02 19:25:11
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-24 22:33:16
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\http;



use CurlHandle;

class Curl
{
    /**
     * UA集合
     */
    public const UA =
    [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1 Edg/103.0.5060.114',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.50',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 QQ/8.0.8.458 V1_IPH_SQ_8.0.8_1_APP_A Pixel/1242 Core/WKWebView Device/Apple(iPhone XS) NetType/WIFI QBWebViewType/1 WKType/1',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Linux; Android 9; COL-AL10 Build/HUAWEICOL-AL10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044813 Mobile Safari/537.36 V1_AND_SQ_8.1.0_1232_YYB_D QQ/8.1.0.4150 NetType/WIFI WebP/0.3.0 Pixel/1080 StatusBarHeight/90 SimpleUISwitch/0',
        'Mozilla/5.0 (Linux; Android 9; COL-AL10 Build/HUAWEICOL-AL10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044607 Mobile Safari/537.36 MMWEBID/7140 MicroMessenger/7.0.4.1420(0x27000437) Process/tools NetType/4G Language/zh_CN',
        'Mozilla/5.0 (Linux; Android 9; MI 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.105 Mobile Safari/537.36',
        'Mozilla/5.0 (Linux; U; Android 9; zh-CN; MI 9 Build/PKQ1.181121.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 Quark/3.4.0.113 Mobile Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.1 Safari/605.1.15',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 SE 2.X MetaSr 1.0',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.1021.400 QQBrowser/9.0.2524.400',
        'Mozilla/5.0 (compatible; Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
        '360spider (http://webscan.360.cn) Google Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Googlebot-Image/1.0 Adwords',
        'AdsBot-Google-Mobile (+http://www.google.com/mobile/adsbot.html) Mozilla (iPhone; U; CPU iPhone OS 3 0 like Mac OS X) AppleWebKit (KHTML, like Gecko) Mobile Safari',
        'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'
    ];

    /**
     * UserAgent in iPhone 12 Pro (Mobile)
     */
    public const UA_MOBILE = self::UA[0];

    /**
     * UserAgent in Edge (PC)
     */
    public const UA_PC = self::UA[1];

    /**
     * 默认请求头
     */
    public const DEFAULT_HEADER =
    [
        'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
        'Accept'           => 'application/json, text/plain, text/html, */*',
        // 'Accept-Encoding'  => 'gzip, deflate, br, identity, compress, *',
        'Accept-Language'  => 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
        'Connection'       => 'keep-alive',
        'Sec-Fetch-Dest'   => 'empty',
        'Sec-Fetch-Mode'   => 'cors',
        'Sec-Fetch-Site'   => 'same-site',
        'Pragma'           => 'no-cache',
        'Cache-Control'    => 'no-cache',
        'User-Agent'       => self::UA_PC,
        'sec-ch-ua'        => 'Chromium";v="110", "Not A(Brand";v="24", "Microsoft Edge";v="110',
        'sec-ch-ua-mobile' => '?0',
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
     * 发送的数据
     *
     * @var string
     */
    protected $sendData;


    public function __construct()
    {
        if($this->curl instanceof CurlHandle) {
            curl_close($this->curl);
        }
        $this->curl = curl_init();
        $this->returnHeader(false)
        ->setTimeout(10)
        ->setReturnTransfer(true)
        ->verifySSL(false)
        ->setHeaders(self::DEFAULT_HEADER);
        $this->headers['sec-ch-ua-platform'] = \owo\get_os();
    }

    /**
     * 返回Curl句柄
     *
     * @return CurlHandle|null
     */
    public function getHandler() : ?CurlHandle
    {
        return $this->curl;
    }

    /**
     * 设置CURL参数
     *
     * @param  int   $option
     * @param  mixed $value
     * @return Curl
     */
    public function setOption(int $option, $value) : Curl
    {
        curl_setopt($this->curl, $option, $value);
        return $this;
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
        $this->setOption(CURLOPT_URL, $url);
        return $this;
    }

    /**
     * 是否检查SSL
     *
     * @param  boolean $_
     * @return Curl
     */
    public function verifySSL(bool $_) : Curl
    {
        $this->setOption(CURLOPT_SSL_VERIFYHOST, $_);
        $this->setOption(CURLOPT_SSL_VERIFYPEER, $_);
        return $this;
    }

    /**
     * 设置Header的单个参数
     *
     * @param  string $index
     * @param  mixed  $value
     * @return Curl
     */
    public function setHeader(string $index, $value) : Curl
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
        /* foreach($headers as $index => $value) {
            if(!is_string($index)) continue;
            $this->headers[$index] = $value;
        } */
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * 设置代理服务器
     *
     * @param  string  $address
     * @param  integer $port
     * @return Curl
     */
    public function setProxy(string $address, int $port) : Curl
    {
        if(\owo\str_is_ip($address) || \owo\str_is_domain($address)) {
            $this->setOption(CURLOPT_PROXY, $address);
            $this->setOption(CURLOPT_PROXYPORT, $port);
        }
        return $this;
    }

    /**
     * 使用随机IP地址
     *
     * @return Curl
     */
    public function setRandomIp() : Curl
    {
        $radomIp = \owo\random_ip();
        return $this->setHeaders([
            'CLIENT-IP'       => $radomIp,
            'X-FORWARDED-FOR' => $radomIp
        ]);
    }

    /**
     * 设置UA (User Agent)
     *
     * @param  string $ua
     * @return Curl
     */
    public function setUA(string $ua) : Curl
    {
        $this->setOption(CURLOPT_USERAGENT, $ua);
        ini_set('user_agent', $ua);
        return $this;
    }

    /**
     * 使用随机UA
     *
     * @return Curl
     */
    public function useRandomUA() : Curl
    {
        $this->setUA(self::UA[array_rand(self::UA)]);
        return $this;
    }

    /**
     * 将UA设置为移动端
     *
     * @return Curl
     */
    public function setUA2Mobile() : Curl
    {
        return $this->setUA(self::UA_MOBILE);
    }

    /**
     * 将UA设置为PC端
     *
     * @return Curl
     */
    public function setUA2PC() : Curl
    {
        return $this->setUA(self::UA_PC);
    }

    /**
     * 设置内容类型
     *
     * @param  string $type
     * @return Curl
     */
    public function setContentType(string $type) : Curl
    {
        return $this->setHeader('Content-Type', $type);
    }

    /**
     * 设置内容长度
     *
     * @param  integer $length
     * @return Curl
     */
    public function setContentLength(int $length) : Curl
    {
        return $this->setHeader('Content-Length', $length);
    }

    /**
     * 设置来源
     *
     * @param  string $referer
     * @return Curl
     */
    public function setReferer(string $referer) : Curl
    {
        $this->setOption(CURLOPT_REFERER, $referer);
        return $this;
    }

    /**
     * 如果为 true, 则以值返回 `curl_exec()` 的结果而不是直接输出
     *
     * @param  boolean $_
     * @return Curl
     */
    public function setReturnTransfer(bool $_) : Curl
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, $_);
        return $this;
    }

    /**
     * 清空Cookie
     *
     * @return Curl
     */
    public function clearCookie() : Curl
    {
        $this->setOption(CURLOPT_COOKIEJAR, '');
        $this->setOption(CURLOPT_COOKIEFILE, '');
        return $this;
    }

    /**
     * 设置原始Cookie数据
     *
     * @param  string $cookie
     * @return Curl
     */
    public function setCookieRaw(string $cookie) : Curl
    {
        $this->setOption(CURLOPT_COOKIE, $cookie);
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
        return $this->setCookieRaw($payload);
    }

    /**
     * 设置Get请求的数据
     *
     * @param  array $data
     * @return Curl
     */
    public function setGetData(array $data) : Curl
    {
        $this->setOption(CURLOPT_URL, $this->url . http_build_query($data));
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
        $this->setOption(CURLOPT_POST, 1);
        $this->sendData = $withJson ? json_encode($data) : http_build_query($data);
        $this->setOption(CURLOPT_POSTFIELDS, $this->sendData);
        $this->setContentLength(strlen($this->sendData));
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
        $this->setOption(CURLOPT_POST, 1);
        $this->sendData = $post;
        $this->setOption(CURLOPT_POSTFIELDS, $this->sendData);
        $this->setContentLength(strlen($this->sendData));
        return $this;
    }

    /**
     * 设置CURL的请求超时时间 (s)
     *
     * @param  int $timeout
     * @return Curl
     */
    public function setTimeout(int $timeout) : Curl
    {
        $this->setOption(CURLOPT_CONNECTTIMEOUT, $timeout);
        $this->setOption(CURLOPT_TIMEOUT, $timeout);
        return $this;
    }

    /**
     * 设置是否返回HTTP响应头数据
     *
     * @param  boolean $_
     * @return Curl
     */
    public function returnHeader(bool $_ = true) : Curl
    {
        $this->returnHeader = $_;
        $this->setOption(CURLOPT_HEADER, $_);
        return $this;
    }

    /**
     * 设置是否返回响应主体
     *
     * @param  boolean $_
     * @return Curl
     */
    public function returnBody(bool $_ = true) : Curl
    {
        $this->setOption(CURLOPT_NOBODY, !$_);
        return $this;
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
        $this->setOption(CURLOPT_HTTPHEADER, $this->formattedHeaders);

        $this->content = curl_exec($this->curl);
        return $this;
    }

    /**
     * 返回CURL执行结果
     *
     * @param  string &$headers
     * @return string
     */
    public function getContent(&$headers = null) : string
    {
        $_ = $this->content;
        if(!$_) {
            return '';
        }
        if($this->returnHeader) {
            $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
            $headers    = substr($_, 0, $headerSize);
            $_          = substr($_, $headerSize);
        }
        return $_ ?? '';
    }

    /**
     * 返回Cookies
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
                $cookie = explode('=', $cookie);
                if(isset($payload[$cookie[0]])) {
                    continue;
                }
                $payload[$cookie[0]] = $cookie[1];
            }
        }
        return $payload;
    }

    /**
     * 以Json格式解码
     *
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
     * @return string|null
     */
    public function getError() : ?string
    {
        return curl_error($this->curl) ?? null;
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
}
?>