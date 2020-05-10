<?php
namespace app;

$http = new \Swoole\Http\Server("0.0.0.0", 9501);

$http->on('request', function($request, $response) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }
    var_dump($request->get, $request->post);
    $rand_num = rand(1000, 9999);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #{$rand_num}</h1>");
});

$http->start();