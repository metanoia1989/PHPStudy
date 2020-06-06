<?php
// 进入等待状态，实现超时等待功能
$server = new Swoole\Http\Server('0.0.0.0', 9502);

$server->on('Request', function ($request, $response) {
    // 等待200ms后向浏览器发送响应
    Swoole\Coroutine\System::sleep(0.2);
    $response->end("<h1>Hello Swoole</h1>");
});

$server->start();