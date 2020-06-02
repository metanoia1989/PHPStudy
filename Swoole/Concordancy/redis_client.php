<?php
Co::set([
    'hook_flags' => SWOOLE_HOOK_TCP
]);

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->set(['enable_coroutine' => true]);

$http->on('request', function ($request, $response) {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379); // 此处产生协程调度，CPU切到下一个协程（下一个请求），不会阻塞进程
    $value = $redis->get('key');  // 此处产生协程调度，CPU切到下一个协程（下一个请求），不会阻塞进程
    $response->end($value);
});

$http->start();