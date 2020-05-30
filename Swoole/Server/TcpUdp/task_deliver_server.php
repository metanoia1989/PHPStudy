<?php
// 任务投递示例
$server = new Swoole\Server('0.0.0.0', 9501, SWOOLE_BASE);

$server->set([
    'worker_num' => 2,
    'task_worker_num' => 4,
]);

$server->on('Receive', function (Swoole\Server $server, $fd, $from_id, $data) {
    echo "接收数据 ".$data."\n";
    $data = trim($data);
    $server->task($data, -1, function (Swoole\Server $server, $task_id, $data) {
        echo "Task Callback: ";
        var_dump($task_id, $data);
    });
    $task_id = $server->task($data, 0);
    $server->send($fd, "分发任务，任务ID为$task_id\n");
});

$server->on('Task', function (Swoole\Server $server, $task_id, $from_id, $data) {
    echo "Tasker进程接收到数据";
    echo "#{$server->worker_id}\tonTask: [PID={$server->worker_id}]: task_id=$task_id, data_len="
        .strlen($data).".".PHP_EOL;
    $server->finish($data);
});

$server->on('workerStart', function ($server, $worker_id) {
    global $argv;
    if ($worker_id >= $server->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]}: task_worker");
    } else {
        swoole_set_process_name("php {$argv[0]}: worker");
    }
});

$server->on('finish', function ($server, $fd, $reactor_id) {

});

$server->start();