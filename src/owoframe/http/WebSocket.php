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
 * @Date         : 2023-02-02 20:22:59
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-02 20:33:45
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 *
 * @link           https://datatracker.ietf.org/doc/html/rfc6455
 * @link           https://learnku.com/articles/36471
 *
 * @notice         当前文件中部分代码来自于互联网, 若侵犯了你的权益, 请联系我删除!
 * @notice         The currently file has some codes copied from Internet,
 *                 If it violates your rights, please contact me to delete it!
 */
declare(strict_types=1);
namespace owoframe\http;

use ErrorException;
use Socket;

class WebSocket
{
    /**
     * 规定密钥字符串
     */
    public const GUID_STRING = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    /**
     * WS绑定监听的IP地址
     *
     * @var string
     */
    private $ip = '0.0.0.0';

    /**
     * WS绑定监听的端口
     *
     * @var integer
     */
    private $port = 32710;

    /**
     * 套接字资源
     *
     * @var resource|Socket|null
     */
    private $socket;

    /**
     * 当Socket正确监听时将会自动转为 True
     *
     * @var boolean
     */
    private $created = false;


    /**
     * 构造函数
     *
     * @param  string  $ip
     * @param  integer $port
     * @param  boolean $autoCreate
     */
    public function __construct(string $ip = '0.0.0.0', int $port = 32710, bool $autoCreate = true)
    {
        $this->ip   = $ip;
        $this->port = $port;

        if($autoCreate) {
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if(!$this->socket) {
                throw new ErrorException('Failed to create socket!');
            }
            socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
            socket_bind($this->socket, $this->ip, $this->port);
            if(!socket_listen($this->socket, $this->port)) {
                throw new ErrorException("Could not bind to address '{$this->getAddress()}'! Error message: " . socket_strerror(socket_last_error($this->socket)));
            }
            $this->created = true;
            \owo\output('[INFO] ', 'OwO WebSocket Server is listening on §3' . $this->toString());
        }
    }

    /**
     * 执行方法
     *
     * @param  callable|null $callback
     * @param  array|null    $args
     * @return void
     */
    public function run(?callable $callback = null, ?array $args = null) : void
    {
        static $isRunning;
        if(!$this->created) {
            throw new ErrorException('Invalid Socket! Error message: ' . socket_strerror(socket_last_error()));
        }
        if($isRunning) {
            throw new ErrorException('Do not call twice this method when WebSocket Server is running!');
        }
        if(!isset($isRunning)) {
            $clients[] = $this->socket;
            $isRunning = true;
        }
        while(true)
        {
            $changes = $clients;
            $write   = $except = [];
            socket_select($changes, $write, $except, null);
            foreach($changes as $ip => $client) {
                // 判断是不是新接入的客户端
                if($this->socket == $client)
                {
                    $newClient = socket_accept($client);
                    if(!($newClient)) {
                        throw new ErrorException('Failed to accept socket: ' . socket_strerror(socket_last_error($client)));
                    }
                    $this->handshaking($newClient, trim(socket_read($newClient, 1024)), $data);
                    socket_getpeername($newClient, $ip);
                    $clients[$ip] = $newClient;
                    \owo\output('[INFO] ', 'New Client connected! IP: ' . $ip);
                    \owo\output('[DEBUG] ', 'User Agent: ' . $data['User-Agent']);
                } else {
                    if(@!socket_recv($client, $data, 2048, 0)) {
                        continue;
                    }
                    $this->decodeData($data);
                    \owo\output('[DEBUG] ', "Received data from Client[{$ip}]: " . $data);
                    $response = 'Hello World! Welcome to use OwOFrame Easy Websocket Widget!';
                    if(is_callable($callback)) {
                        $response = call_user_func_array($callback, $args) ?? $response;
                    }
                    $this->send($client, $response);
                    \owo\output('[DEBUG] ', 'Response to Client with: ' . $response);
                }
            }
        }
    }

    /**
     * 握手处理
     *
     * @param Socket $newClient
     * @return int|false
     */
    public function handshaking($newClient, string $line, &$clientData = [])
    {
        $lines = preg_split("/\r\n/", $line);
        foreach($lines as $line) {
            if(preg_match('/\A(\S+): (.*)\z/', rtrim($line), $matches)) {
                $clientData[$matches[1]] = $matches[2];
            }
        }
        if(!isset($clientData['Sec-WebSocket-Key'])) {
            $errorMsg = 'NO SECRET WEBSOCKET KEY IS GIVEN!!!';
            \owo\output('[ERROR] ', "Could not make HandShake to address '{$this->getAddress()}'! Error message: {$errorMsg}");
            return socket_write($newClient, $errorMsg, strlen($errorMsg));
        }

        $secretKey = $clientData['Sec-WebSocket-Key'];
        $headers   = [
            'Upgrade'              => 'websocket',
            'Connection'           => 'Upgrade',
            'WebSocket-Origin'     => $this->getIp(),
            'WebSocket-Location'   => $this->toString(),
            'Sec-WebSocket-Accept' => base64_encode(pack('H*', sha1($secretKey . self::GUID_STRING))),
        ];
        $formatted = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n";
        foreach($headers as $k => $v) {
            $formatted .= "{$k}: {$v}\r\n";
        }
        $formatted .= "\r\n";
        return socket_write($newClient, $formatted, strlen($formatted));
    }

    /**
     * 解析接收数据
     *
     * @param string $buffer
     * @return string|null
     */
    public function decodeData(string &$buffer)
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if ($len === 126)  {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127)  {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else  {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        $buffer = $decoded;
    }

    /**
     * 发送数据
     *
     * @param Socket $client 新接入的socket
     * @param string $data   要发送的数据
     *
     * @link  https://www.sky8g.com/technology/2048/
     *
     * @return int|false
     */
    public function send($client, string $data)
    {
        $header = chr(0x81);
        $header_length = 1;

        // Payload length: 7 bits, 7+16 bits, or 7+64 bits
        $dataLength = strlen($data);

        // The length of the payload data, in bytes: if 0-125, that is the payload length.
        if($dataLength <= 125) {
            $header[1] = chr($dataLength);
            $header_length = 2;
        }
        elseif($dataLength <= 65535) {
            // If 126, the following 2 bytes interpreted as a 16
            // bit unsigned integer are the payload length.
            $header[1] = chr(126);
            $header[2] = chr($dataLength >> 8);
            $header[3] = chr($dataLength & 0xFF);
            $header_length = 4;
        } else {
            // If 127, the following 8 bytes interpreted as a 64-bit unsigned integer (the
            // most significant bit MUST be 0) are the payload length.
            $header[1] = chr(127);
            $header[2] = chr(($dataLength & 0xFF00000000000000) >> 56);
            $header[3] = chr(($dataLength & 0xFF000000000000) >> 48);
            $header[4] = chr(($dataLength & 0xFF0000000000) >> 40);
            $header[5] = chr(($dataLength & 0xFF00000000) >> 32);
            $header[6] = chr(($dataLength & 0xFF000000) >> 24);
            $header[7] = chr(($dataLength & 0xFF0000) >> 16);
            $header[8] = chr(($dataLength & 0xFF00 ) >> 8);
            $header[9] = chr( $dataLength & 0xFF );
            $header_length = 10;
        }

        return socket_write($client, $header . $data, strlen($data) + $header_length);
    }

    /**
     * 关闭socket连接
     *
     * @return void
     */
    public function close() : void
    {
        socket_close($this->socket);
    }

    /**
     * 返回监听IP地址
     *
     * @return string
     */
    public function getIp() : string
    {
        return $this->ip;
    }

    /**
     * 返回监听端口
     *
     * @return int
     */
    public function getPort() : int
    {
        return $this->port;
    }

    /**
     * 返回监听地址
     *
     * @return string
     */
    public function getAddress() : string
    {
        return $this->getIp() . ':' . $this->getPort();
    }

    /**
     * 返回完整地址
     *
     * @return string
     */
    public function toString() : string
    {
        return 'ws://' . $this->getAddress();
    }
}
?>