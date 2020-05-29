<?php
// Swoole\Server 的属性

// $setting
// Server::set() 函数所设置的参数会保存到 Server->$setting 属性上。在回调函数中可以访问运行参数的值。
// Swoole\Server->setting

$server = new Swoole\Server('0.0.0.0', 9501);
$server->set(['worke_num' => 4]);

echo "Swoole\Server的设置如下：\n";
print_r($server->setting);

// $master_pid
// 返回当前服务器主进程的 PID。
// Swoole\Server->master_pid
// 只能在 onStart/onWorkerStart 之后获取到

// $manager_pid
// 返回当前服务器管理进程的 PID。
// Swoole\Server->manager_pid
// 只能在 onStart/onWorkerStart 之后获取到
$server->on("start", function ($server) {
    echo "服务器主进程PID：".$server->master_pid."\n";
    echo "服务器管理进程PID：".$server->manager_pid."\n";
});

$server->on('receive', function ($server, $fd, $reactor_id, $data) {
    echo "当前工作进程编号：".$server->worker_id."\n";
    echo "当前进程是".$server->taskworker ? "Task工作进程" : "Worker进程"."\n";

    foreach ($server->connections as $fd) {
        echo "连接ID ".$fd."\n";
    }
    echo "当前服务器共有 ".count($server->connections)." 个连接\n";

    $server->send($fd, 'Swoole: '.$data."\n");
    $server->close($fd);
});

// $worker_id
// 得到当前 Worker 进程的编号，包括 Task 进程。
// Swoole\Server->worker_id: int;

// $taskworker
// 当前进程是否是 Task 进程。
// Swoole\Server->taskworker: bool;
// 返回值
// true 表示当前的进程是 Task 工作进程
// false 表示当前的进程是 Worker 进程

// $connections
// TCP 连接迭代器，可以使用 foreach 遍历服务器当前所有的连接，此属性的功能与 Server->getClientList 是一致的，但是更加友好。
// 遍历的元素为单个连接的 fd。
// Swoole\Server->connections
// $connections 属性是一个迭代器对象，不是 PHP 数组，所以不能用 var_dump 或者数组下标来访问，只能通过 foreach 进行遍历操作
// Base 模式
// SWOOLE_BASE 模式下不支持跨进程操作 TCP 连接，因此在 BASE 模式中，只能在当前进程内使用 $connections 迭代器

// $ports
// 监听端口数组，如果服务器监听了多个端口可以遍历 Server::$ports 得到所有 Swoole\Server\Port 对象。
// 其中 swoole_server::$ports[0] 为构造方法所设置的主服务器端口。
$ports = $server->ports;
var_dump($ports);

$server->start();