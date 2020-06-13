<?php
function test()
{
    throw new \RuntimeException(__FILE__, __LINE__);
}

Swoole\Coroutine::create(function () {
    try {
        test();
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }
});