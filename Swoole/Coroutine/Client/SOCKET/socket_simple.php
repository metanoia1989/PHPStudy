<?php
$socket = new Co\Socket(AF_INET, SOCK_STREAM, 0);

go(function () use ($socket) {
    $retval = $socket->connect('localhost', 9501);
    while ($retval) {
        $n = $socket->send('hello');
        var_dump($n);

        $data = $socket->recv();
        var_dump($data);

        if (empty($data)) { // 发生错误或对端关闭连接，本端也需要关闭
            $socket->close();
            break;
        }
        co::sleep(1.0);
    }
    var_dump($retval, $socket->errCode);
});