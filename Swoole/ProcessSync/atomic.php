<?php

$atomic = new Swoole\Atomic();

$serv = new Swoole\Server('0.0.0.0', '9501');
$serv->set([
    'worker_num' => 1,
    'log_file' => '/dev/null',
]);
$serv->on('start', function ($serv) use ($atomic) {
    if ($atomic->add() == 2) {
        $serv->shutdown();
    }
});

$serv->on('ManagerStart', function ($serv) use ($atomic) {
    if ($atomic->add() == 2) {
        $serv->shutdown();
    }
});

$serv->on('ManagerStop', function ($serv) {
    echo "shutdown\n";
});

$serv->on('Receive', function () {}); 

$serv->start();
