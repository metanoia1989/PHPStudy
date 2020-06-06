<?php
// 协程容器创建并发协程
$shceduler = new Swoole\Coroutine\Scheduler;
$shceduler->parallel(10, function ($t, $n) {
    Co::sleep($t);
    echo "Co ".Co::getCid()."\n";
}, 0.05, 'A');
$shceduler->start();