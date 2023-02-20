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
 * @Date         : 2023-02-01 23:15:25
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-14 19:36:19
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);



namespace owo
{
    if(!defined('owohttp'))        define('owohttp', 'owosuperget');
    if(!defined('GET_MODE'))       define('GET_MODE',       0);
    if(!defined('POST_MODE'))      define('POST_MODE',      1);
    if(!defined('AJAX_GET_MODE'))  define('AJAX_GET_MODE',  2);
    if(!defined('AJAX_POST_MODE')) define('AJAX_POST_MODE', 3);

    /**
     * 代理方法: 检测传参
     *
     * @param  string  $index
     * @param  boolean $autoUpper
     * @param  array   $SU_GLOBAL_VAR
     * @param  mixed   $default
     * @return mixed
     */
    function _proxy(string $index, array $SU_GLOBAL_VAR = [], $default = [], bool $autoUpper = false)
    {
        if(strtolower($index) === owohttp) {
            return $SU_GLOBAL_VAR;
        }
        $index = $autoUpper ? strtoupper($index) : $index;
        return $SU_GLOBAL_VAR[$index] ?? ($default ?? null);
    }

    /**
     * 代理方法: 获取Server的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function server(string $index, $default = null)
    {
        return _proxy($index, $_SERVER, $default, true);
    }

    /**
     * 代理方法: 获取Session的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function session(string $index, $default = null)
    {
        return _proxy($index, $_SESSION, $default);
    }

    /**
     * 代理方法: 获取Cookie的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function cookie(string $index, $default = null)
    {
        return _proxy($index, $_COOKIE, $default);
    }

    /**
     * 代理方法: 获取Get的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function get(string $index, $default = null)
    {
        return _proxy($index, $_GET, $default);
    }

    /**
     * 代理方法: 获取Post的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function post(string $index, $default = null)
    {
        return _proxy($index, $_POST, $default);
    }

    /**
     * 代理方法: 获取Files的值
     *
     * @param  string $index
     * @param  string $default
     * @return mixed
     */
    function files(string $index, $default = null)
    {
        return _proxy($index, $_FILES, $default);
    }

    /**
     * 返回并过滤指定的 SERVER 名称
     *
     * @param  string  $index
     * @param  integer $filter
     * @return void
     */
    function filter_server(string $index, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return filter_input(INPUT_SERVER, $index, $filter);
    }

    /**
     * 返回并过滤指定的 COOKIE 名称
     *
     * @param  string  $index
     * @param  integer $filter
     * @return void
     */
    function filter_cookie(string $index, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return filter_input(INPUT_COOKIE, $index, $filter);
    }

    /**
     * 返回并过滤指定的 GET 名称
     *
     * @param  string  $index
     * @param  integer $filter
     * @return void
     */
    function filter_get(string $index, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return filter_input(INPUT_GET, $index, $filter);
    }

    /**
     * 返回并过滤指定的 POST 名称
     *
     * @param  string  $index
     * @param  integer $filter
     * @return void
     */
    function filter_post(string $index, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return filter_input(INPUT_POST, $index, $filter);
    }

    /**
     * 检查指定值是否在某一个请求数据中
     *
     * @param  string  $index
     * @param  boolean $autoUpper
     * @param  mixed   $method
     * @return string
     */
    function get_http_mode(string $index, bool $autoUpper = false, &$method = null) : string
    {
        if($autoUpper) $index = strtoupper($index);
        if(get($index)) {
            $method = 'GET';
        }
        elseif(post($index)) {
            $method = 'POST';
        }
        elseif(files($index)) {
            $method = 'FILE';
        } else {
            $method = 'GET';
        }
        return $method;
    }

    /**
     * 返回请求模式的整型代码
     *
     * @return integer
     */
    function get_http_mode2int() : int
    {
        $httpMode = strtolower(server('REQUEST_METHOD'));
        $ajaxMode = server('HTTP_X_REQUESTED_WITH') && (strtolower(server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest');

        switch($httpMode) {
            default:
            return -1;

            case 'get':
            return $ajaxMode ? AJAX_GET_MODE : GET_MODE;

            case 'post':
            return $ajaxMode ? AJAX_POST_MODE : POST_MODE;
        }
    }

    /**
     * 判断当前HTTP请求模式是否为 Get
     *
     * @return boolean
     */
    function is_http_get(bool $allowAjax = true) : bool
    {
        return (get_http_mode2int() === GET_MODE) || ($allowAjax && (get_http_mode2int() === AJAX_GET_MODE));
    }

    /**
     * 判断当前HTTP请求模式是否为 Post
     *
     * @return boolean
     */
    function is_http_post(bool $allowAjax = true) : bool
    {
        return (get_http_mode2int() === POST_MODE) || ($allowAjax && (get_http_mode2int() === AJAX_POST_MODE));
    }

    /**
     * 通过 php://input 获取 HTTP_RAW_DATA
     *
     * * 修复了对前端使用fetch等方法时PHP无法取到数据的情况
     *
     * @return string|null
     */
    function fetch() : ?string
    {
        return file_get_contents('php://input') ?? null;
    }

    /**
     * 获取客户端IP
     *
     * @return string
     */
    function get_client_ip() : string
    {
        if(server('HTTP_CLIENT_IP'))           $ip = server('HTTP_CLIENT_IP');
        elseif(server('HTTP_X_FORWARDED_FOR')) $ip = server('HTTP_X_FORWARDED_FOR');
        elseif(server('REMOTE_ADDR'))          $ip = server('REMOTE_ADDR');
        else                                   $ip = 'Unknown';
        return $ip;
    }

    /**
     * 获取客户端UA
     *
     * @return string
     */
    function get_client_ua() : string
    {
        if(!empty(server('HTTP_USER_AGENT')))
        {
            $br = server('HTTP_USER_AGENT');
            if(preg_match('/MSIE/i',$br))        $br = 'MSIE';
            elseif(preg_match('/Firefox/i',$br)) $br = 'Firefox';
            elseif(preg_match('/Chrome/i',$br))  $br = 'Chrome';
            elseif(preg_match('/Safari/i',$br))  $br = 'Safari';
            elseif(preg_match('/Opera/i',$br))   $br = 'Opera';
            else $br = 'Other';
            return $br;
        }
        else return 'FAILED';
    }

    /**
     * 判断客户端是否为移动设备
     */
    function is_mobile_ua() : bool
    {
        if(server('HTTP_X_WAP_PROFILE') || server('HTTP_VIA')) return true;
        return (bool) preg_match(
            '/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|' .
            'opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc|' .
            'smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i'
        , server('HTTP_USER_AGENT'));
    }

    /**
     * 生成随机IP
     *
     * @return string
     */
    function random_ip() : string
    {
        $ipLib =
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
        $rand_key = mt_rand(0, 9);
        return long2ip(mt_rand((int) $ipLib[$rand_key][0], (int) $ipLib[$rand_key][1]));
    }

    /**
     * 判断传入的字符串是否为有效的IP地址
     *
     * @param  string  $str
     * @return boolean
     */
    function str_is_ip(string $str) : bool
    {
        return (bool) preg_match("/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/", $str);
    }

    /**
     * 判断字符串是否为Url地址
     *
     * @param  string  $str
     * @return boolean
     */
    function str_is_url(string $str) : bool
    {
        return (bool) preg_match('/^((http|https):\/\/)?\w+\.\w+\//iU', $str);
    }

    /**
     * 判断字符串是否为多级域名格式
     *
     * @param  string  $str
     * @param  array   $match
     * @return boolean
     */
    function str_is_domain(string $str, array &$match = ['localhost']) : bool
    {
        $str = str_replace(['http', 'https', '://'], '', trim($str));
        if(in_array($str, $match)) {
            $match = $str;
            return true;
        }
        // * Regex verified: https://regex101.com/r/rhSD1e/1
        return !str_has($str, '--') && (bool) preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?[\.]*)+?([a-z]+)$/i', $str, $match);
    }

    /**
     * 判断是否为HTTPS协议
     *
     * @return boolean
     */
    function is_https() : bool
    {
        return (!empty(server('HTTPS')) && 'off' != strtolower(server('HTTPS')))
            || (!empty(server('SERVER_PORT')) && 443 == server('SERVER_PORT'));
    }

    /**
     * 检查是否是一个安全的主机名
     *
     * @param  string  $str
     * @return boolean
     */
    function str_is_safe_host(string $str) : bool
    {
        if($str === 'localhost') return false;

        $address = gethostbyname($str);
        $inet    = inet_pton($address);

        if($inet === false) {
            # @see https://www.php.net/manual/zh/function.dns-get-record.php
            $records = dns_get_record($str, DNS_AAAA);
            if(empty($records)) return false;
            // 有可能是ipv6的地址
            $address = $records[0]['ipv6'];
            $inet = inet_pton($address);
        }

        if(str_has($address, '.'))
        {
            // ipv4, 非公网地址
            $privateNetworks =
            [
                '10.0.0.0|10.255.255.255',
                '172.16.0.0|172.31.255.255',
                '192.168.0.0|192.168.255.255',
                '169.254.0.0|169.254.255.255',
                '127.0.0.0|127.255.255.255'
            ];
            $long = ip2long($address);

            foreach($privateNetworks as $network) {
                [$from, $to] = explode('|', $network);
                if($long >= ip2long($from) && $long <= ip2long($to)) return false;
            }
        } else {
            // ipv6, https://en.wikipedia.org/wiki/Private_network
            $from = inet_pton('fd00::');
            $to = inet_pton('fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
            if(($inet >= $from) && ($inet <= $to)) return false;
        }
        return true;
    }

    /**
     * 获取完整请求HTTP地址
     *
     * @return string
     */
    function get_complete_uri() : string
    {
        return server('REQUEST_SCHEME') . '://' . server('HTTP_HOST') . server('REQUEST_URI');
    }

    /**
     * 获取根地址
     *
     * @return string
     */
    function get_scheme_host() : string
    {
        return server('REQUEST_SCHEME') . '://' . server('HTTP_HOST');
    }

    /**
     * 返回自定义Url
     *
     * @param  string $name
     * @param  string $path
     * @return string
     */
    function get_custom_url(string $name, string $path) : string
    {
        return trim($path, '/') . '/' . str_replace('//', '/', ltrim(((strpos($name, './') === 0) ? substr($name, 2) : $name), '/'));
    }

    /**
     * 返回URL访问路径
     *
     * @return string
     */
    function get_path_info() : string
    {
        return str_replace(server('SCRIPT_NAME', ''), '', server('REQUEST_URI', ''));
    }

    /**
     * 返回纯路径
     *
     * @return string
     */
    function get_raw_path() : string
    {
        return parse_url(get_path_info(), PHP_URL_PATH);
    }

    /**
     * 返回路径切片
     *
     * @param  integer $level
     * @return array
     */
    function get_path(int $level = 0) : array
    {
        return array_slice(str_split(get_raw_path()), $level);
    }
}
?>