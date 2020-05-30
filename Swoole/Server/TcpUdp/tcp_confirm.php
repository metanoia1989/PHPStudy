<?php
$serv = new Swoole\Server('0.0.0.0', 9501);
$serv->set([
    // 'enable_delay_receive' => true,
]);

// 监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    // 检测客户端链接$fd，进行权限验证、身份校验等操作
    // ....
    // 必须是swoole 4.5 版本以上才有用，用宝塔装的是4.4的版本
    $isSuccess = $serv->confirm($fd); // 没问题则确认连接
    if ($isSuccess) {
        $serv->send($fd, "Server: 连接成功！！！");
    } else {
        $serv->send($fd, "Server: 连接失败！！！");
    }
});

// 监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

// 监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->start();