<?php
$timer = Swoole\Timer::after(1000, function () {
    echo "timeout\n";
});
var_dump(Swoole\Timer::clear($timer));
var_dump($timer);