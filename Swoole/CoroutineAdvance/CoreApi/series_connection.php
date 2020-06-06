<?php
// 串联多个协程调用栈
go(function () {
    go(function () {
        $ptrace = Co::getBackTrace(Co::getPcid());
        var_dump(array_merge($ptrace, Co::getBackTrace(Co::getCid())));
    });
});