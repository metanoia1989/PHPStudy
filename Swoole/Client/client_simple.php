<?php
// 不支持 Apache 的 prework 多线程模式
$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send('hello world\n');
echo $client->recv();
$client->close();