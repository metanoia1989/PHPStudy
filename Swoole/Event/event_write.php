<?php
// Event::write 函数可以将 stream/sockets 资源的数据发送变成异步的，当缓冲区满了或者返回 EAGAIN，Swoole 底层会将数据加入到发送队列，并监听可写。
// socket 可写时 Swoole 底层会自动写入

use Swoole\Event;

$fp = stream_socket_client('tcp://127.0.0.1:9501');
$data = str_repeat('A', 1024 * 1024 * 2);

Event::add($fp, function ($fp) {
    echo fread($fp, 1024 * 1024 * 2);
});

Event::write($fp, $data);