<?php
function route()
{
    controller();
}

function controller()
{
    your_code();
}

function your_code()
{
    co::sleep(.001);
    exit(1);
}

go(function () {
    try {
        route();
    } catch (\Swoole\ExitException $e) {
        var_dump($e->getMessage());
        var_dump($e->getStatus() === 1);
        var_dump($e->getFlags() === SWOOLE_EXIT_IN_COROUTINE);
    }
});