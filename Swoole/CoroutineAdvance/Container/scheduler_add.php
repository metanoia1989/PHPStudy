<?php
// 协程容器添加任务
$scheduler = new Co\Scheduler;
$scheduler->set([
    'max_coroutine' => 100
]);

$scheduler->add(function ($a, $b) {
    Co::sleep(1);
    echo assert($a == 'hello').PHP_EOL;
    echo assert($b == 12345).PHP_EOL;
    echo "Done.\n";
}, 'hello', 12345);
$scheduler->start();