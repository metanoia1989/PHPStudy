<?php
Co\run(function () {
    $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
    $client->set([
        'open_length_check' => 1,
        'package_length_type' => 'N',
        'package_length_offset' => 0, // 第N个字节是包长度的值
        'package_body_offset' => 4, // 第几个字节开始计算长度
        'package_max_length' => 2000000, // 协议最大长度
    ]);
    if (!$client->connect('0.0.0.0', 9501, 0.5)) {
        echo "connect failed. Error: {$client->errCode}\n";
    }
    $client->send("hello world\n");
    echo $client->recv();
    $client->close();
});