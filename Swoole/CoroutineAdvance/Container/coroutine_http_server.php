<?php
// 全协程的HTTP服务器
Co\run(function () {
    $server = new Co\Http\Server("0.0.0.0", 9502, false);
    $server->handle('/', function ($request, $response) {
        $response->end('<h1>Index</h1>');
    });
    $server->handle('/test', function ($request, $response) {
        $response->end('<h1>Test</h1>');
    });
    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end('<h1>Stop</h1>');
        $server->shutdown();
    });
    $server->start();
});

echo "不会被执行";