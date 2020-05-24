<?php

$server = new \Swoole\Server("localhost", 9999, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

// 混合使用UDP/TCP，同时监听内网和外网端口
$server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP); // 添加TCP
$server->addListener("192.168.0.103", 9503, SWOOLE_SOCK_TCP); // 添加Web Socket
$server->addListener("0.0.0.0", 9504, SWOOLE_SOCK_UDP); // udp
$server->addListener("/var/run/myserv.sock", 0, SWOOLE_UNIX_STREAM); // UnixSocket Stream
$server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP | SWOOLE_SSL); // TCP + SSL

$port = $server->addListener("0.0.0.0", 0, SWOOLE_SOCK_TCP); // 系统随机分配端口，返回值为随机分配的端口
echo $port->port;

// 用于设置运行时的各项参数
// 服务器启动后通过 $serv->setting 来访问 Server->set 方法设置的参数数组。
// Server->set 必须在 Server->start 前调用
$server->set([
    'reactor_num' => 2, // 调节主进程内事件处理线程的数量
    'worker_num' => 4, // 启动的 Worker 进程数
    'backlog' => 128, // 设置 Listen 队列长度，此参数将决定最多同时有多少个等待 accept 的连接。
    'max_request' => 50, // 设置 worker 进程的最大任务数。一个 worker 进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源
    'dispatch_mode' => 1, // 数据包分发策略。
]);

// on() 注册 Server 的事件回调函数。
// 重复调用 on 方法时会覆盖上一次的设定
// 大小写不敏感，事件名称字符串不要加 on