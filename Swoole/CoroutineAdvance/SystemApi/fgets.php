<?php
// 协程方式按行读取文件
$fp = fopen(__FILE__, 'r') ;
go(function () use ($fp) {
    $r = Swoole\Coroutine\System::fgets($fp);
    var_dump($r);
    fclose($fp);
});