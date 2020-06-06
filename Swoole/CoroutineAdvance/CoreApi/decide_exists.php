<?php
// 判断协程是否存在
go(function () {
    go(function () {
        go(function () {
            Co::sleep(0.001);
            echo "1: ";
            var_dump(Co::exists(Co::getPcid())); 
        });
        go(function () {
            Co::sleep(0.003);
            echo "2: ";
            var_dump(Co::exists(Co::getPcid())); 
        });
        Co::sleep(0.002);
        echo "3: ";
        var_dump(Co::exists(Co::getPcid())); 
    });
});

var_dump(Co::getPcid());
go(function () {
    var_dump(Co::getPcid());
    go(function () {
        var_dump(Co::getPcid());
        go(function () {
            var_dump(Co::getPcid());
            go(function () {
                var_dump(Co::getPcid());
            });
            go(function () {
                var_dump(Co::getPcid());
            });
            go(function () {
                var_dump(Co::getPcid());
            });
        });
        var_dump(Co::getPcid());
    });
    var_dump(Co::getPcid());
});
var_dump(Co::getPcid());