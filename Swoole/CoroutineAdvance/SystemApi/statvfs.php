<?php
go(function () {
    $info = Swoole\Coroutine\System::statvfs('/');
    var_dump($info);
});