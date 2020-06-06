<?php
// 执行一条shell指令
go(function () {
    $ret = Swoole\Coroutine\System::exec("md5sum ".__FILE__);
    var_dump($ret);
});