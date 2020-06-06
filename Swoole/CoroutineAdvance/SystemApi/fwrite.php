<?php
// 协程方式向文件写入数据
$fp = fopen(__DIR__."/test.txt", "a+");
go(function () use ($fp) {
    $r = Swoole\Coroutine\System::fwrite($fp, "hello world\n", 5);
    var_dump($r);
    fclose($fp);
});