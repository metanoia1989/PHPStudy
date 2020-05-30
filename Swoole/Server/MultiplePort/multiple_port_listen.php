<?php
// Server 可以监听多个端口，每个端口都可以设置不同的协议处理方式，例如 80 端口处理 http 协议，9507 端口处理 TCP 协议。SSL/TLS 传输加密也可以只对特定的端口启用。
//例如主服务器是 WebSocket 或 HTTP 协议，新监听的 TCP 端口（listen 的返回值，即 Swoole\Server\Port，以下简称 port）默认会继承主 Server 的协议设置。
// 必须单独调用 port 对象的 set 方法和 On 方法设置新的协议才会启用新协议 ，port 对象的 set 和 on 方法，使用方法与基类 Swoole\Server 完全一致。

$server = new Swoole\Server('0.0.0.0',9998); 
$server->on('receive', function (Swoole\Server $server, $fd, $reactor_id, $data) {});

// 监听新端口
// 返回port对象
$port1 = $server->listen("0.0.0.0", 9501, SWOOLE_SOCK_TCP);
$port2 = $server->listen("0.0.0.0", 9502, SWOOLE_SOCK_UDP);
$port3 = $server->listen("0.0.0.0", 9503, SWOOLE_SOCK_TCP | SWOOLE_SSL);


// 设置网络协议
// port对象的调用set方法
$port1->set([
    'open_length_check' => true,
    'package_length_type' => 'N',
    'package_length_offset' => 0,
    'package_max_length' => 800000,
]);

$port3->set([
    'open_eof_split' => true,
    'package_eof' => "\r\n",
    'ssl_cert_file' => 'ssl.cert',
    'ssl_key_file' => 'ssl.key',
]);

// 设置回调函数
// 设置每个port的回调函数
$port1->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});

$port1->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});

$port1->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$port2->on('packet', function ($serv, $data, $addr) {
    var_dump($data, $addr);
});

$server->start();