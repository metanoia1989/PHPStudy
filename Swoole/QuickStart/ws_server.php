<?php
namespace app;

// 创建websocket服务器对象
$ws = new \Swoole\WebSocket\Server("0.0.0.0", 9501);

// 监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello, welcome\n");
});

// 监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    echo "Message : {$frame->data}\n";
    $ws->push($frame->fd, "Server: {$frame->data}");
});

// 监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed.\n";
});

$ws->start();