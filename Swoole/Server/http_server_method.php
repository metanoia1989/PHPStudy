<?php

// Http\Server
// Http\Server 对 HTTP 协议的支持并不完整，一定要作为应用服务器处理动态请求。并且在前端增加 Nginx 作为代理
// Http\Server 继承自 Server，所以 Server 提供的所有 API 和配置项都可以使用，进程模型也是一致的。请参考 Server 章节。
// 内置 HTTP 服务器的支持，通过几行代码即可写出一个高并发，高性能，异步 IO 的多进程 HTTP 服务器。
$http = new Swoole\Http\Server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();
