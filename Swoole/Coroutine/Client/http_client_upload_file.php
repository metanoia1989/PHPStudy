<?php
Co\run(function () {
    $cli = new Swoole\Coroutine\Http\Client('httpbin.org', 80);
    $cli->setHeaders([
        'Host' => 'httpbin.org',
    ]);
    $cli->set(['timeout' => -1]);
    // $cli->addFile(__FILE__, 'file1', 'text/plain');
    $cli->addData(Co::readFile(__FILE__), 'file1', 'text/plain');
    $cli->post('/post', ['foo' => 'bar']);
    echo $cli->body;
    $cli->close();
});