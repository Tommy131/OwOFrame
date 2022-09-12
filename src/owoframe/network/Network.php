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

namespace owoframe\network;


class Network
{
    public const IP_LONG =
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
     * 判断传入的字符串是否为有效IP地址
     *
     * @param  string $str
     * @return boolean
     */
    public static function isIp(string $str) : bool
    {
        return (bool) preg_match("/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/", $str);
    }

    /**
     * 检查是否是一个安全的主机名
     *
     * @author *
     * @param  string $str
     * @return boolean
     */
    public static function isSafeHost(string $str) : bool
    {
        if($str === 'localhost') return false;

        $address = gethostbyname($str);
        $inet    = inet_pton($address);

        if($inet === false) {
            // 有可能是ipv6的地址;
            // @see https://www.php.net/manual/zh/function.dns-get-record.php
            $records = dns_get_record($str, DNS_AAAA);
            if(empty($records)) return false;
            $address = $records[0]['ipv6'];
            $inet = inet_pton($address);
        }

        if(strpos($address, '.') !== false)
        {
            // ipv4, 非公网地址;
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
            // ipv6, https://en.wikipedia.org/wiki/Private_network;
            $from = inet_pton('fd00::');
            $to = inet_pton('fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
            if($inet >= $from && $inet <= $to) return false;
        }
        return true;
    }

    /**
     * 返回随机IP地址
     *
     * @return string
     */
    public static function getRadomIp() : string
    {
        $rand_key = mt_rand(0, 9);
        return long2ip(mt_rand((int) self::IP_LONG[$rand_key][0], (int) self::IP_LONG[$rand_key][1]));
    }
}