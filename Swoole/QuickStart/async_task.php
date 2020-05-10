<?php
namespace app;

$serv = new \Swoole\Server("127.0.0.1", 9501);

// 设置异步任务的工作进程数量
$serv->set(['task_worker_num' => 4]);

// 此回调函数在worker进程中执行
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    // 投递异步任务
    $task_id = $serv->task($data);
    echo "Dispatch AsyncTask: id=$task_id\n";
});

// 处理异步任务，回调函数在此task进程中执行
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
    // 返回任务执行的结果
    $serv->finish("$data -> OK");
});

// 处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

$serv->start();