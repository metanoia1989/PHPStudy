<?php
// 协程并发
Co\run(function () {
    go(function () {
        var_dump(file_get_contents('http://www.xinhuanet.com/'));
    });
    go(function () {
        Co::sleep(1);
        echo "done\n";
    });
});

echo "可以得到执行";