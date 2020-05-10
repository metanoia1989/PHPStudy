<?php
namespace app;

\Swoole\Runtime::enableCoroutine(); // 开启协程HOOK
$t = microtime(true);

\Co\run(function() {
    for ($c = 100; $c--; ) {
        go(function() {
            for ($n = 100; $n--; ) 
                usleep(1000);
        });
    }
});

echo 'use ' . (microtime(true) - $s) . ' s';