<?php
// 打包 WebSocket 消息
$ws = new swoole_server('0.0.0.0', 9501 , SWOOLE_BASE);
$ws->set(array(
    'log_file' => '/dev/null'
));
$ws->on('WorkerStart', function (\swoole_server $serv) {
});

$ws->on('receive', function ($serv, $fd, $threadId, $data) {
    $sendData = "HTTP/1.1 101 Switching Protocols\r\n";
    $sendData .= "Upgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: IFpdKwYy9wdo4gTldFLHFh3xQE0=\r\n";
    $sendData .= "Sec-WebSocket-Version: 13\r\nServer: swoole-http-server\r\n\r\n";
    $sendData .= swoole_websocket_server::pack("hello world\n");
    $serv->send($fd, $sendData);
});

$ws->start();