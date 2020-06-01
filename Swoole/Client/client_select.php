<?php
$clients = [];

for ($i = 0; $i < 20; $i++) {
    $client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC); // 同步阻塞
    $ret = $client->connect('127.0.0.1', 9501, 0.5, 0);
    if (!$ret) {
        echo "connect failed. Error: {$client->errCode}\n";
    } else {
        $client->send("hello world\n");
        $clients[$client->sock] = $client;
    }
}

while (!empty($clients)) {
    $write = $error = [];
    $read = array_values($clients);
    $n = swoole_client_select($read, $write, $error, 0.6);
    if ($n > 0) {
        foreach ($read as $index => $c) {
            echo "Recv #{$c->sock}: ".$c->recv()."\n";
            unset($clients[$c->sock]);
        }
    }
}