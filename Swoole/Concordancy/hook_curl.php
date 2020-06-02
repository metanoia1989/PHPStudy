<?php
Co::set(['hook_flags' => SWOOLE_HOOK_CURL]);
Co\run(function () {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.baidu.com/");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    var_dump($result);
});