<?php
// 零毫秒一次性定时器
Swoole\Event::defer(function () {
    echo "hello\n";
});

// tick 定时器
Swoole\Timer::tick(1000, function () {
    echo "timeout\n";
});

Swoole\Timer::tick(3000, function (int $timer_id, $param1, $param2) {
    echo "timer_id #$timer_id, after 3000ms.\n";
    echo "param1 is $param1, param2 is $param2.\n";

    Swoole\Timer::tick(14000, function ($timer_id) {
        echo "timer_id #$timer_id, after 14000ms.\n";
    });
}, "A", "B");

// after 一次性定时器
$str = "Swoole";
Swoole\Timer::after(1000, function () use ($str) {
    echo "Hello, $str\n";
});