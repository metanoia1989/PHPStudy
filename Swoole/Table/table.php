<?php
$table = new Swoole\Table(1024);
$table->column('fd', Swoole\Table::TYPE_INT);
$table->column('reactor_id', Swoole\Table::TYPE_INT);
$table->column('data', Swoole\Table::TYPE_STRING, 64);
$table->create();

$serv = new Swoole\Server('0.0.0.0', 9501);
$serv->set(['dispatch_mode' => 1]);
$serv->table = $table;

$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
    $cmd = explode(' ', trim($data));

    // get
    if ($cmd[0] == 'get') {
        if (count($cmd) < 2) {
            $cmd[1] = $fd;
        }
        $get_fd = intval($cmd[1]);
        $info = $serv->table->get($get_fd);
        $serv->send($fd, var_export($info, true)."\n");
    } elseif ($cmd[0] == 'set') {
        $ret = $serv->table->set($fd, [
            'reactor_id' => $data,
            'fd' => $fd,
            'data' => $cmd[1]
        ]);
        if ($ret === false) {
            $serv->send($fd, "ERROR\n");
        } else {
            $serv->send($fd, "OK\n");
        }
    } else {
        $serv->send($fd, "command error.\n");
    }

});

$serv->start();