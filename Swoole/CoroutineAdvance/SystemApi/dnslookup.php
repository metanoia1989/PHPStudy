<?php
// 域名地址查询
go(function () {
    $ip = Swoole\Coroutine\System::dnsLookup('www.baidu.com');
    echo $ip;
});