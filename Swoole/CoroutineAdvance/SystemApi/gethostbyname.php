<?php
// 根据域名获取IP地址
go(function () {
    $ip = Swoole\Coroutine\System::gethostbyname('www.baidu.com', AF_INET, 0.5);
    echo $ip;
});