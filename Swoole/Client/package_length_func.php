<?php
$client = new Swoole\Client(SWOOLE_SOCK_TCP);
$client->set([
    'open_length_check' => true,
    'package_length_func' => function ($data) {
        if (strlen($data) < 8) {
            return 0;
        }
        $length = intval(trim(substr($data, 0, 8)));
        if ($length <= 0) {
            return -1;
        }
        return $length + 8;
    }
]);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send('hello world\n');
echo $client->recv();
$client->close();