<?php
// 协程方式写文件
$filename = __DIR__."/test.txt";
go(function () use ($filename) {
    $w = Swoole\Coroutine\System::writeFile($filename, "hello swoole!");
    var_dump($w);
});