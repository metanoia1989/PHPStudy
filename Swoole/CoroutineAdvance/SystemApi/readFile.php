<?php
// 协程方式一次性读取文件
go(function () {
    $r = Swoole\Coroutine\System::readFile(__FILE__);
    var_dump($r);
});