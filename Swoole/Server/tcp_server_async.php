<?php

$server = new \Swoole\Server("localhost", 9999, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

// 混合使用UDP/TCP，同时监听内网和外网端口
$server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP); // 添加TCP
$server->addListener("192.168.0.103", 9503, SWOOLE_SOCK_TCP); // 添加Web Socket
$server->addListener("0.0.0.0", 9504, SWOOLE_SOCK_UDP); // udp
$server->addListener("/var/run/myserv.sock", 0, SWOOLE_UNIX_STREAM); // UnixSocket Stream
$server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP | SWOOLE_SSL); // TCP + SSL

$port = $server->addListener("0.0.0.0", 0, SWOOLE_SOCK_TCP); // 系统随机分配端口，返回值为随机分配的端口
echo $port->port;
