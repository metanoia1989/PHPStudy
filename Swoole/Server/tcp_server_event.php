<?php
// Swoole Server 事件
// 所有的 Swoole 的回调函数，每个回调函数都是一个 PHP 函数，对应一个事件。

// onStart
// 启动后在主进程（master）的主线程回调此函数
// function onStart(Swoole\Server $server);
// 在此事件之前 Server 已进行了如下操作
// - 启动创建完成 manager 进程
// - 启动创建完成 worker 子进程
// - 监听所有 TCP/UDP/unixSocket 端口，但未开始 Accept 连接和请求
// - 监听了定时器
// 接下来要执行
// - 主 Reactor 开始接收事件，客户端可以 connect 到 Server
// *限制*
// onStart 回调中，仅允许 echo、打印 Log、修改进程名称。不得执行其他操作 (不能调用 server 相关函数等操作，因为服务尚未就绪)。
// onWorkerStart 和 onStart 回调是在不同进程中并行执行的，不存在先后顺序。
// 可以在 onStart 回调中，将 $serv->master_pid 和 $serv->manager_pid 的值保存到一个文件中。
// 这样可以编写脚本，向这两个 PID 发送信号来实现关闭和重启的操作。
// onStart 事件在 Master 进程的主线程中被调用。
// *共享全局对象*
// 在 onStart 中创建的全局资源对象不能在 Worker 进程中被使用，因为发生 onStart 调用时，worker 进程已经创建好了
// 新创建的对象在主进程内，Worker 进程无法访问到此内存区域
// 因此全局对象创建的代码需要放置在 Server::start 之前，典型的例子是 Swoole\Table
// *安全提示*
// 在 onStart 回调中可以使用异步和协程的 API，但需要注意这可能会与 dispatch_func 和 package_length_func 存在冲突，请勿同时使用。
// onStart 回调在 return 之前服务器程序不会接受任何客户端连接，因此可以安全地使用同步阻塞的函数。
// *BASE 模式*
// SWOOLE_BASE 模式下没有 master 进程，因此不存在 onStart 事件，请不要在 BASE 模式中使用使用 onStart 回调函数。

// onShutdown
// 此事件在 Server 正常结束时发生
// function onShutdown(Swoole\Server $server);
// 在此之前 Swoole\Server 已进行了如下操作
// - 已关闭所有 Reactor 线程、HeartbeatCheck 线程、UdpRecv 线程
// - 已关闭所有 Worker 进程、 Task 进程、User 进程
// - 已 close 所有 TCP/UDP/UnixSocket 监听端口
// - 已关闭主 Reactor
// *不会被触发的情况*
// 强制 kill 进程不会回调 onShutdown，如 kill -9
// 需要使用 kill -15 来发送 SIGTREM 信号到主进程才能按照正常的流程终止
// 在命令行中使用 Ctrl+C 中断程序会立即停止，底层不会回调 onShutdown
// *注意事项*
// 请勿在 onShutdown 中调用任何异步或协程相关 API，触发 onShutdown 时底层已销毁了所有事件循环设施。

// onWorkerStart
// 此事件在 Worker 进程 / Task 进程启动时发生，这里创建的对象可以在进程生命周期内使用。
// function onWorkerStart(Swoole\Server $server, int $workerId);
// onWorkerStart/onStart 是并发执行的，没有先后顺序
// 可以通过 $server->taskworker 属性来判断当前是 Worker 进程还是 Task 进程
// 设置了 worker_num 和 task_worker_num 超过 1 时，每个进程都会触发一次 onWorkerStart 事件，可通过判断 $worker_id 区分不同的工作进程
// 由 worker 进程向 task 进程发送任务，task 进程处理完全部任务之后通过 onFinish 回调函数通知 worker 进程。
// 例如，我们在后台操作向十万个用户群发通知邮件，操作完成后操作的状态显示为发送中，这时我们可以继续其他操作，
// 等邮件群发完毕后，操作的状态自动改为已发送。
// *为 Worker 进程 / Task 进程重命名* 
$server->on('WorkerStart', function ($server, $worker_id){
    global $argv;
    if($worker_id >= $server->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]} task worker");
    } else {
        swoole_set_process_name("php {$argv[0]} event worker");
    }
});
// *关于重载的问题*
// 如果想使用 Reload 机制实现代码重载入，必须在 onWorkerStart 中 require 你的业务文件，而不是在文件头部。
// 在 onWorkerStart 调用之前已包含的文件，不会重新载入代码。
// 可以将公用的、不易变的 php 文件放置到 onWorkerStart 之前。这样虽然不能重载入代码，但所有 Worker 是共享的，
// 不需要额外的内存来保存这些数据。 onWorkerStart 之后的代码每个进程都需要在内存中保存一份
// *worker_id*
// - $worker_id 表示这个 Worker 进程的 ID，范围参考 $worker_id
// - $worker_id 和进程 PID 没有任何关系，可使用 posix_getpid 函数获取 PID
// *协程支持*
// 在 onWorkerStart 回调函数中会自动创建协程，所以 onWorkerStart 可以调用协程 API
// *注意*
// 发生致命错误或者代码中主动调用 exit 时，Worker/Task 进程会退出，管理进程会重新创建新的进程。这可能导致死循环，不停地创建销毁进程


// onWorkerStop
// 此事件在 Worker 进程终止时发生。在此函数中可以回收 Worker 进程申请的各类资源。
// function onWorkerStop(Swoole\Server $server, int $workerId);
// *注意*
// - 进程异常结束，如被强制 kill、致命错误、core dump 时无法执行 onWorkerStop 回调函数。
// - 请勿在 onWorkerStop 中调用任何异步或协程相关 API，触发 onWorkerStop 时底层已销毁了所有事件循环设施。

// onWorkerExit
// 仅在开启 reload_async 特性后有效。参见 如何正确的重启服务
// function onWorkerExit(Swoole\Server $server, int $workerId);
// *注意*
// - Worker 进程未退出，onWorkerExit 会持续触发
// - onWorkerExit 仅在 Worker 进程内触发， Task 进程不执行 onWorkerExit
// - 在 onWorkerExit 中尽可能地移除 / 关闭异步的 Socket 连接，最终底层检测到事件循环中事件监听的句柄数量为 0 时退出进程
// - 等待 Worker 进程退出后才会执行 onWorkerStop 事件回调

// onConnect
// 有新的连接进入时，在 worker 进程中回调。
// function onConnect(Swoole\Server $server, int $fd, int $reactorId);
// *dispatch_mode = 1/3*
// 在此模式下 onConnect/onReceive/onClose 可能会被投递到不同的进程。连接相关的 PHP 对象数据，无法实现在 onConnect 回调初始化数据，onClose 清理数据
// onConnect/onReceive/onClose 3 种事件可能会并发执行，可能会带来异常
// *注意*
// onConnect/onClose 这 2 个回调发生在 worker 进程内，而不是主进程。
// UDP 协议下只有 onReceive 事件，没有 onConnect/onClose 事件

// onReceive
// 接收到数据时回调此函数，发生在 worker 进程中。
// function onReceive(Swoole\Server $server, int $fd, int $reactorId, string $data);
// *关于 TCP 协议下包完整性，参考 TCP 粘包问题*
// 使用底层提供的 open_eof_check/open_length_check/open_http_protocol 等配置可以保证数据包的完整性
// 不使用底层的协议处理，在 onReceive 后 PHP 代码中自行对数据分析，合并 / 拆分数据包。
// 例如：代码中可以增加一个 $buffer = array()，使用 $fd 作为 key，来保存上下文数据。 
// 每次收到数据进行字符串拼接，$buffer[$fd] .= $data，然后在判断 $buffer[$fd] 字符串是否为一个完整的数据包。
// 默认情况下，同一个 fd 会被分配到同一个 Worker 中，所以数据可以拼接起来。
// 使用 dispatch_mode = 3 时。 请求数据是抢占式的，同一个 fd 发来的数据可能会被分到不同的进程。所以无法使用上述的数据包拼接方法
// *多端口监听，参考此节*
// 当主服务器设置了协议后，额外监听的端口默认会继承主服务器的设置。需要显式调用 set 方法来重新设置端口的协议。
$server = new Swoole\Http\Server("127.0.0.1", 9501);
$port2 = $server->listen('127.0.0.1', 9502, SWOOLE_SOCK_TCP);
$port2->on('receive', function (Swoole\Server $server, $fd, $reactor_id, $data) {
    echo "[#".$server->worker_id."]\tClient[$fd]: $data\n";
});
// 这里虽然调用了 on 方法注册了 onReceive 回调函数，但由于没有调用 set 方法覆盖主服务器的协议，
// 新监听的 9502 端口依然使用 HTTP 协议。使用 telnet 客户端连接 9502 端口发送字符串时服务器不会触发 onReceive。
// *注意*
// 未开启自动协议选项，onReceive 单次收到的数据最大为 64K
// 开启了自动协议处理选项，onReceive 将收到完整的数据包，最大不超过 package_max_length
// 支持二进制格式，$data 可能是二进制数据

// onPacket
// 接收到 UDP 数据包时回调此函数，发生在 worker 进程中。
// function onPacket(Swoole\Server $server, string $data, array $clientInfo);
// 服务器同时监听 TCP/UDP 端口时，收到 TCP 协议的数据会回调 onReceive，收到 UDP 数据包回调 onPacket。 
// 服务器设置的 EOF 或 Length 等自动协议处理 (见)，对 UDP 端口是无效的，因为 UDP 包本身存在消息边界，不需要额外的协议处理。

// onClose
// TCP 客户端连接关闭后，在 worker 进程中回调此函数。
// function onClose(Swoole\Server $server, int $fd, int $reactorId);
// *提示*
// - 主动关闭
//   当服务器主动关闭连接时，底层会设置此参数为 -1，可以通过判断 $reactorId < 0 来分辨关闭是由服务器端还是客户端发起的。
//   只有在 PHP 代码中主动调用 close 方法被视为主动关闭
// - 心跳检测
//   心跳检测是由心跳检测线程通知关闭的，关闭时 onClose 的 $reactorId 参数不为 -1
// *注意*
// - onClose 回调函数如果发生了致命错误，会导致连接泄漏。通过 netstat 命令会看到大量 CLOSE_WAIT 状态的 TCP 连接 ，参考 Swoole 视频教程
// - 无论由客户端发起 close 还是服务器端主动调用 $server->close() 关闭连接，都会触发此事件。因此只要连接关闭，就一定会回调此函数
// - onClose 中依然可以调用 getClientInfo 方法获取到连接信息，在 onClose 回调函数执行完毕后才会调用 close 关闭 TCP 连接
// - 这里回调 onClose 时表示客户端连接已经关闭，所以无需执行 $server->close($fd)。代码中执行 $server->close($fd) 会抛出 PHP 错误警告。

// onTask
// 在 task 进程内被调用。worker 进程可以使用 task 函数向 task_worker 进程投递新的任务。
// 当前的 Task 进程在调用 onTask 回调函数时会将进程状态切换为忙碌，这时将不再接收新的 Task，
// 当 onTask 函数返回时会将进程状态切换为空闲然后继续接收新的 Task。
// function onTask(Swoole\Server $server, int $task_id, int $src_worker_id, mixed $data);
// *提示*
// V4.2.12 起如果开启了 task_enable_coroutine 则回调函数原型是
$server->on('Task', function (Swoole\Server $server, Swoole\Server\Task $task) {
    $task->worker_id;              //来自哪个`Worker`进程
    $task->id;                     //任务的编号
    $task->flags;                  //任务的类型，taskwait, task, taskCo, taskWaitMulti 可能使用不同的 flags
    $task->data;                   //任务的数据
    co::sleep(0.2);                //协程 API
    $task->finish([123, 'hello']); //完成任务，结束并返回数据
});
// *返回执行结果到 worker 进程*
// 在 onTask 函数中 return 字符串，表示将此内容返回给 worker 进程。
// worker 进程中会触发 onFinish 函数，表示投递的 task 已完成，
// 当然你也可以通过 Swoole\Server->finish() 来触发 onFinish 函数，而无需再 return
// return 的变量可以是任意非 null 的 PHP 变量
// *注意*
// onTask 函数执行时遇到致命错误退出，或者被外部进程强制 kill，当前的任务会被丢弃，但不会影响其他正在排队的 Task

// onFinish
// 此回调函数在 worker 进程被调用，当 worker 进程投递的任务在 task 进程中完成时， 
// task 进程会通过 Swoole\Server->finish() 方法将任务处理的结果发送给 worker 进程。
// function onFinish(Swoole\Server $server, int $task_id, string $data)
// *注意*
// - task 进程的 onTask 事件中没有调用 finish 方法或者 return 结果，worker 进程不会触发 onFinish
// - 执行 onFinish 逻辑的 worker 进程与下发 task 任务的 worker 进程是同一个进程

// onPipeMessage
// 当工作进程收到由 $server->sendMessage() 发送的 unixSocket 消息时会触发 onPipeMessage 事件。
// worker/task 进程都可能会触发 onPipeMessage 事件
// function onPipeMessage(Swoole\Server $server, int $src_worker_id, mixed $message);

// onWorkerError
// 当 Worker/Task 进程发生异常后会在 Manager 进程内回调此函数。
// function onWorkerError(Swoole\Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal);
// 此函数主要用于报警和监控，一旦发现 Worker 进程异常退出，那么很有可能是遇到了致命错误或者进程 CoreDump。通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
// *常见错误*
// - signal = 11：说明 Worker 进程发生了 segment fault 段错误，可能触发了底层的 BUG，请收集 core dump 信息和 valgrind 内存检测日志，向我们反馈此问题
// - exit_code = 255：说明 Worker 进程发生了 Fatal Error 致命错误，请检查 PHP 的错误日志，找到存在问题的 PHP 代码，进行解决
// - signal = 9：说明 Worker 被系统强行 Kill，请检查是否有人为的 kill -9 操作，检查 dmesg 信息中是否存在 OOM（Out of memory）
// - 如果存在 OOM，分配了过大的内存。是否创建了非常大的 Swoole\Table 内存模块。

// onManagerStart
// 当管理进程启动时触发此事件
// function onManagerStart(Swoole\Server $server);
// *提示*
// - 在这个回调函数中可以修改管理进程的名称。
// - 在 4.2.12 以前的版本中 manager 进程中不能添加定时器，不能投递 task 任务、不能用协程。
// - 在 4.2.12 或更高版本中 manager 进程可以使用基于信号实现的同步模式定时器
// - manager 进程中可以调用 sendMessage 接口向其他工作进程发送消息
// *启动顺序*
// - Task 和 Worker 进程已创建
// - Master 进程状态不明，因为 Manager 与 Master 是并行的，onManagerStart 回调发生是不能确定 Master 进程是否已就绪
// *BASE 模式*
// 在 SWOOLE_BASE 模式下，如果设置了 worker_num、max_request、task_worker_num 参数，
// 底层将创建 manager 进程来管理工作进程。因此会触发 onManagerStart 和 onManagerStop 事件回调。

// onManagerStop
// 当管理进程结束时触发
// function onManagerStop(Swoole\Server $serv);
// *提示*
// onManagerStop 触发时，说明 Task 和 Worker 进程已结束运行，已被 Manager 进程回收。

// onBeforeReload
// Worker 进程 Reload 之前触发此事件，在 Manager 进程中回调
// function onBeforeReload(Swoole\Server $serv);

// onAfterReload
// Worker 进程 Reload 之后触发此事件，在 Manager 进程中回调
// function onAfterReload(Swoole\Server $serv);

// 事件执行顺序
// - 所有事件回调均在 $server->start 后发生
// - 服务器关闭程序终止时最后一次事件是 onShutdown
// - 服务器启动成功后，onStart/onManagerStart/onWorkerStart 会在不同的进程内并发执行
// - onReceive/onConnect/onClose 在 Worker 进程中触发
// - Worker/Task 进程启动 / 结束时会分别调用一次 onWorkerStart/onWorkerStop
// - onTask 事件仅在 task 进程中发生
// - onFinish 事件仅在 worker 进程中发生
// - onStart/onManagerStart/onWorkerStart 3 个事件的执行顺序是不确定的