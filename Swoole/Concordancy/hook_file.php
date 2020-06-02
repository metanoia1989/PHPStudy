<?php
Co::set(['hook_flags' => SWOOLE_HOOK_FILE]);
Co\run(function () {
    $fp = fopen('test.log', 'a+');
    fwrite($fp, str_repeat('A', 2048)."\n");
    fwrite($fp, str_repeat('B', 2048)."\n");
});