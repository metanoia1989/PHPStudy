<?php
// 将一个 socket 加入到底层的 reactor 事件监听中
$fp = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
fwrite($fp, "GET / HTTP/1.1\r\nHost: www.qq.com\r\n\r\n");

Swoole\Event::add($fp, function ($fp) {
    $resp = fread($fp, 8192);
    // socket处理完成后，从epoll事件中移除socket
    Swoole\Event::del($fp);
    fclose($fp);
}, null, SWOOLE_EVENT_READ);

// 检测传入的 $fd 是否已加入了事件监听。
var_dump(Swoole\Event::isset($fp, SWOOLE_EVENT_READ));
var_dump(Swoole\Event::isset($fp, SWOOLE_EVENT_WRITE));
var_dump(Swoole\Event::isset($fp, SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE));
