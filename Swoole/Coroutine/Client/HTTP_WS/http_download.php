<?php
Co\run(function () {
    $host = 'www.swoole.com';
    $client = new Swoole\Coroutine\Http\Client($host, 443, true);
    $client->set(['timeout' => -1]);
    $client->setHeaders([
        'Host' => $host,
        'User-Agent' => 'Chrome/49.0.2587.3',
        'Accept' => '*',
        'Accept-Encoding' => 'gzip'
    ]);
    $client->download('/static/files/swoole-logo.svg', __DIR__.'/logo.svg');
});