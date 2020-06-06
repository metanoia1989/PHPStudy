<?php
// Swoole协程是单进程单线程模型，优先执行子协程
go(function () {
    go(function () {
        co::sleep(3.0);
        go(function () {
            co::sleep(2.0);
            echo "co[3] end\n";
        });
        echo "co[2] end\n";
    });

    co::sleep(1.0);
    echo "co[1] end\n";
});

go(function () {
    defer(function () {
        echo "释放资源成功！\n";
    });
});