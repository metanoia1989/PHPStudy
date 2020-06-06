<?php
// 获取域名的所有ip
go(function () {
    $ips = Swoole\Coroutine\System::getaddrinfo('www.baidu.com');
    var_dump($ips);
});