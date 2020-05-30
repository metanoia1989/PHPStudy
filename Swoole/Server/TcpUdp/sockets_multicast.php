<?php

$server = new Swoole\Server('0.0.0.0', 9905, SWOOLE_BASE, SWOOLE_SOCK_UDP);
$server->set(['worker_num' => 1]);
$socket = $server->getSocket(); // 获取底层的socket句柄

$ret = socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, [
    'group' => '224.10.20.30', // 组播地址
    'interface' => 'enp0s3', // 网络接口名称，可以为数字或字符串，如eht0、wlan0
]);

if ($ret === false) {
    throw new RuntimeException('Unable to join multicast group.');
}

$server->on('Packet', function (Swoole\Server $server, $data, $addr) {
    $server->sendto($addr['address'], $addr['port'], "Swoole: $data");
    var_dump($addr, strlen($data));
});

$server->start();