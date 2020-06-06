<?php
// 协程方式读取文件
$fp = fopen(__FILE__, 'r') ;
go(function () use ($fp) {
    $r = Swoole\Coroutine\System::fread($fp);
    var_dump($r);
    fclose($fp);
});