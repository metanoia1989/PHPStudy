<?php

// Swoole\Server
// 此节包含 Swoole\Server 类的全部方法、属性、配置项以及所有的事件。
// Swoole\Server 类是所有异步风格服务器的基类，后面章节的 Http\Server、WebSocket\Server、Redis\Server 都继承于它。

require "../vendor/autoload.php";

$server = new \Swoole\Server("localhost", 9999, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

// 混合使用UDP/TCP，同时监听内网和外网端口
$server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP); // 添加TCP
$server->addListener("192.168.0.103", 9503, SWOOLE_SOCK_TCP); // 添加Web Socket
$server->addListener("0.0.0.0", 9504, SWOOLE_SOCK_UDP); // udp
// $server->addListener("/var/run/myserv.sock", 0, SWOOLE_UNIX_STREAM); // UnixSocket Stream
// $server->addListener("127.0.0.1", 9502, SWOOLE_SOCK_TCP | SWOOLE_SSL); // TCP + SSL

$port = $server->addListener("0.0.0.0", 0, SWOOLE_SOCK_TCP); // 系统随机分配端口，返回值为随机分配的端口
echo $port->port;

// 用于设置运行时的各项参数
// 服务器启动后通过 $serv->setting 来访问 Server->set 方法设置的参数数组。
// Server->set 必须在 Server->start 前调用
$server->set([
    'reactor_num' => 2, // 调节主进程内事件处理线程的数量
    'worker_num' => 4, // 启动的 Worker 进程数
    'task_worker_num' => 2,
    'backlog' => 128, // 设置 Listen 队列长度，此参数将决定最多同时有多少个等待 accept 的连接。
    'max_request' => 50, // 设置 worker 进程的最大任务数。一个 worker 进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源
    'dispatch_mode' => 2, // 数据包分发策略。
    'max_wait_time' => 60,
    'reload_async' => true,
]);

// on() 注册 Server 的事件回调函数。
// Swoole\Server->on(string $event, mixed $callback): void
// 重复调用 on 方法时会覆盖上一次的设定
// 大小写不敏感，事件名称字符串不要加 on
// $server->on('connect', function ($server, $fd) {
//     echo "Client: Conenct.\n";
// });

// addListener()
// 增加监听的端口。业务代码中可以通过调用 Server->getClientInfo 来获取某个连接来自于哪个端口。
// Swoole\Server->addListener(string $host, int $port, $type = SWOOLE_SOCK_TCP): bool|Swoole\Server\Port
// 监听 1024 以下的端口需要 root 权限
// 主服务器是 WebSocket 或 HTTP 协议，新监听的 TCP 端口默认会继承主 Server 的协议设置。必须单独调用 set 方法设置新的协议才会启用新协议

// listen() 此方法是 addlistener 的别名。
// Swoole\Server->listen(string $host, int $port, $type = SWOOLE_SOCK_TCP): bool|Swoole\Server\Port

// addProcess()
// 添加一个用户自定义的工作进程。此函数通常用于创建一个特殊的工作进程，用于监控、上报或者其他特殊的任务。
// Swoole\Server->addProcess(Swoole\Process $process): bool
// 不需要执行 start。在 Server 启动时会自动创建进程，并执行指定的子进程函数
// - 创建的子进程可以调用 $server 对象提供的各个方法，如 getClientList/getClientInfo/stats
// - 在 Worker/Task 进程中可以调用 $process 提供的方法与子进程进行通信
// - 在用户自定义进程中可以调用 $server->sendMessage 与 Worker/Task 进程通信
// - 用户进程内不能使用 Server->task/taskwait 接口
// - 用户进程内可以使用 Server->send/close 等接口
// - 用户进程内应当进行 while(true)(如下边的示例) 或 EventLoop 循环 (例如创建个定时器)，否则用户进程会不停地退出重启
// 用户进程的生命周期
// - 用户进程的生存周期与 Master 和 Manager 是相同的，不会受到 reload 影响
// - 用户进程不受 reload 指令控制，reload 时不会向用户进程发送任何信息
// - 在 shutdown 关闭服务器时，会向用户进程发送 SIGTERM 信号，关闭用户进程
// - 自定义进程会托管到 Manager 进程，如果发生致命错误，Manager 进程会重新创建一个
$process = new Swoole\Process(function ($process) use ($server) {
    $socket = $process->exportSocket();
    while (true) {
        $msg = $socket->recv();
        foreach ($server->connections as $conn) {
            $server->send($conn, $msg);
        }
    }
}, false, 2, 1);
$server->addProcess($process);
// $server->on('receive', function ($serv, $fd, $reactor_id, $data) use ($process) {
//     // 定时向客户端发送消息
//     $serv->tick(1000, function () use ($serv, $fd) {
//         $serv->send($fd, "hello client，this is from server random number: ".rand(1, 100)."\n");
//     });

//     // 群接收到的消息
//     $socket = $process->exportSocket();
//     $socket->send($data);

// });

// start()
// 启动服务器，监听所有 TCP/UDP 端口。
// 提示：以下以 SWOOLE_PROCESS 模式为例
// - 启动成功后会创建 worker_num+2 个进程。Master 进程 +Manager 进程 +serv->worker_num 个 Worker 进程。
// - 启动失败会立即返回 false
// - 启动成功后将进入事件循环，等待客户端连接请求。start 方法之后的代码不会执行
// - 服务器关闭后，start 函数返回 true，并继续向下执行
// - 设置了 task_worker_num 会增加相应数量的 Task 进程
// - 方法列表中 start 之前的方法仅可在 start 调用前使用，在 start 之后的方法仅可在 onWorkerStart、onReceive 等事件回调函数中使用
// 扩展说明
// - Master 主进程 主进程内有多个 Reactor 线程，基于 epoll/kqueue 进行网络事件轮询。收到数据后转发到 Worker 进程去处理
// - Manager 进程 对所有 Worker 进程进行管理，Worker 进程生命周期结束或者发生异常时自动回收，并创建新的 Worker 进程
// - Worker 进程 对收到的数据进行处理，包括协议解析和响应请求。未设置 worker_num，底层会启动与 CPU 数量一致的 Worker 进程。
//       启动失败扩展内会抛出致命错误，请检查 php error_log 的相关信息。errno={number} 是标准的 Linux Errno，可参考相关文档。
//       如果开启了 log_file 设置，信息会打印到指定的 Log 文件中。
// 启动失败常见错误
// - bind 端口失败，原因是其他进程已占用了此端口
// - 未设置必选回调函数，启动失败
// - PHP 代码存在致命错误，请检查 PHP 错误信息 php_errors.log
// - 执行 ulimit -c unlimited，打开 core dump，查看是否有段错误
// - 关闭 daemonize，关闭 log，使错误信息可以打印到屏幕

// reload()
// 安全地重启所有 Worker/Task 进程。
// Swoole\Server->reload(bool $only_reload_taskworkrer = false): bool
// 例如：一台繁忙的后端服务器随时都在处理请求，如果管理员通过 kill 进程方式来终止 / 重启服务器程序，可能导致刚好代码执行到一半终止。
// 这种情况下会产生数据的不一致。如交易系统中，支付逻辑的下一段是发货，假设在支付逻辑之后进程被终止了。会导致用户支付了货币，但并没有发货，后果非常严重。
// Swoole 提供了柔性终止 / 重启的机制，管理员只需要向 Server 发送特定的信号，Server 的 Worker 进程可以安全的结束。
// 首先要注意新修改的代码必须要在 OnWorkerStart 事件中重新载入才会生效，比如某个类在 OnWorkerStart 之前就通过 composer 的 autoload 载入了就是不可以的。
// 其次 reload 还要配合这两个参数 max_wait_time 和 reload_async，设置了这两个参数之后就能实现异步安全重启。
// 如果没有此特性，Worker 进程收到重启信号或达到 max_request 时，会立即停止服务，这时 Worker 进程内可能仍然有事件监听，这些异步任务将会被丢弃。设置上述参数后会先创建新的 Worker，旧的 Worker 在完成所有事件之后自行退出，即 reload_async。
// 如果旧的 Worker 一直不退出，底层还增加了一个定时器，在约定的时间 ( max_wait_time 秒) 内旧的 Worker 没有退出，底层会强行终止。
// 说明：
// -reload 有保护机制，当一次 reload 正在进行时，收到新的重启信号会丢弃
// - 如果设置了 user/group，Worker 进程可能没有权限向 master 进程发送信息，这种情况下必须使用 root 账户，在 shell 中执行 kill 指令进行重启
// -reload 指令对 addProcess 添加的用户进程无效
// 发送信号:
// - SIGTERM: 向主进程 / 管理进程发送此信号服务器将安全终止
// - 在 PHP 代码中可以调用 $serv->shutdown() 完成此操作
// - SIGUSR1: 向主进程 / 管理进程发送 SIGUSR1 信号，将平稳地 restart 所有 Worker 进程
// - SIGUSR2: 向主进程 / 管理进程发送 SIGUSR2 信号，将平稳地重启所有 Task 进程
// - 在 PHP 代码中可以调用 $serv->reload() 完成此操作
// Process 模式：
// 在 Process 启动的进程中，来自客户端的 TCP 连接是在 Master 进程内维持的，worker 进程的重启和异常退出，不会影响连接本身。
// Base 模式
// - 在 Base 模式下，客户端连接直接维持在 Worker 进程中，因此 reload 时会切断所有连接。
// - Base 模式不支持 reload Task 进程
// Reload 有效范围:
// - Reload 操作只能重新载入 Worker 进程启动后加载的 PHP 文件，使用 get_included_files 函数来列出哪些文件是在 WorkerStart 
// 之前就加载的 PHP 文件，在此列表中的 PHP 文件，即使进行了 reload 操作也无法重新载入。要关闭服务器重新启动才能生效。
$server->on('WorkerStart', function(Swoole\Server $server, int $workerId) {
    // echo "此数据中的文件表示进程启动前就加载了，所以无法reload：\n";
    // echo json_encode(get_included_files());
    if (!$server->taskworker) {
        $timerId = $server->tick(1000, function ($id) {
            echo "定时器id: $id\n";
        });
        $server->after(3000, function () use ($server, $timerId) {
            echo "工作进程启动了 欧耶！！！\n";
            echo "清除定时器：\n";
            $server->clearTimer($timerId);
        });
    } else {
        // $server->tick(1000, function () {
        //     echo "任务中的定时器任务执行\n";
        // });
        $server->after(1500, function () {
            echo "任务中的定时器任务执行\n";
        });
    }
});
// PC/OpCache：
// 如果 PHP 开启了 APC/OpCache，reload 重载入时会受到影响，有 2 种解决方案
// 1. 打开 APC/OpCache 的 stat 检测，如果发现文件更新 APC/OpCache 会自动更新 OpCode
// 2. 在 onWorkerStart 中加载文件（require、include 等函数）之前执行 apc_clear_cache 或 opcache_reset 刷新 OpCode 缓存
// 注意：
// - 平滑重启只对 onWorkerStart 或 onReceive 等在 Worker 进程中 include/require 的 PHP 文件有效
// -Server 启动前就已经 include/require 的 PHP 文件，不能通过平滑重启重新加载
// - 对于 Server 的配置即 $serv->set() 中传入的参数设置，必须关闭 / 重启整个 Server 才可以重新加载
// -Server 可以监听一个内网端口，然后可以接收远程的控制命令，去重启所有 Worker 进程

// stop()
// 使当前 Worker 进程停止运行，并立即触发 onWorkerStop 回调函数。
// Swoole\Server->stop(int $workerId = -1, bool $waitEvent = false): bool
// - 异步 IO 服务器在调用 stop 退出进程时，可能仍然有事件在等待。比如使用了 Swoole\MySQL->query，
//   发送了 SQL 语句，但还在等待 MySQL 服务器返回结果。这时如果进程强制退出，SQL 的执行结果就会丢失了。
// - 设置 $waitEvent = true 后，底层会使用异步安全重启策略。先通知 Manager 进程，重新启动一个新的 Worker 
//   来处理新的请求。当前旧的 Worker 会等待事件，直到事件循环为空或者超过 max_wait_time 后，退出进程，最大限度的保证异步事件的安全性。

// shutdown()
// 关闭服务。
// Swoole\Server->shutdown(): void
// 此函数可以用在 Worker 进程内
// 向主进程发送 SIGTERM 也可以实现关闭服务
// kill -15 主进程PID

// tick()
// 添加 tick 定时器，可以自定义回调函数。此函数是 Swoole\Timer::tick 的别名。
// Swoole\Server->tick(int $millisecond, mixed $callback): void
// - Worker 进程结束运行后，所有定时器都会自动销毁
// - tick/after 定时器不能在 Server->start 之前使用

// after()
// 添加一个一次性定时器，执行完成后就会销毁。此函数是 Swoole\Timer::after 的别名。
// - 定时器的生命周期是进程级的，当使用 reload 或 kill 重启关闭进程时，定时器会全部被销毁
// - 如果有某些定时器存在关键逻辑和数据，请在 onWorkerStop 回调函数中实现，或参考 如何正确的重启服务

// defer()
// 延后执行一个函数，是 Event::defer 的别名。
// Swoole\Server->defer(callable $callback): void
// - 底层会在 EventLoop 循环完成后执行此函数。此函数的目的是为了让一些 PHP 代码延后执行，程序优先处理其他的 IO 事件。
//   比如某个回调函数有 CPU 密集计算又不是很着急，可以让进程处理完其他的事件再去 CPU 密集计算
// - 底层不保证 defer 的函数会立即执行，如果是系统关键逻辑，需要尽快执行，请使用 after 定时器实现
// - 在 onWorkerStart 回调中执行 defer 时，必须要等到有事件发生才会回调
function query($server, $db) {
  $server->defer(function() use ($db) {
      $db->close();
  });
}

// clearTimer()
// 清除 tick/after 定时器，此函数是 Swoole\Timer::clear 的别名。
// Swoole\Server->clearTimer(int $timerId): bool
// clearTimer 仅可用于清除当前进程的定时器

// close()
// 关闭客户端连接。
// Swoole\Server->close(int $fd, bool $reset = false): bool
// - Server 主动 close 连接，也一样会触发 onClose 事件
// - 不要在 close 之后写清理逻辑。应当放置到 onClose 回调中处理
// - HTTP\Server 的 fd 在上层回调方法的 response 中获取
$server->on('request', function ($request, $response) use ($server) {
    $server->close($response->fd);
});

// send()
// 向客户端发送数据。
// Swoole\Server->send(int $fd, string $data, int $serverSocket  = -1): bool
// 发送过程是异步的，底层会自动监听可写，将数据逐步发送给客户端，也就是说不是 send 返回后对端就收到数据了。
// =安全性=
// * send 操作具有原子性，多个进程同时调用 send 向同一个 TCP 连接发送数据，不会发生数据混杂
// =长度限制=
// * 如果要发送超过 2M 的数据，可以将数据写入临时文件，然后通过 sendfile 接口进行发送
// * 通过设置 buffer_output_size 参数可以修改发送长度的限制
// * 在发送超过 8K 的数据时，底层会启用 Worker 进程的共享内存，需要进行一次 Mutex->lock 操作
// =缓存区=
// * 当 Worker 进程的 unixSocket 缓存区已满时，发送 8K 数据将启用临时文件存储
// * 如果连续向同一个客户端发送大量数据，客户端来不及接收会导致 Socket 内存缓存区塞满，Swoole 底层会立即返回 false,false 时可以将数据保存到磁盘，等待客户端收完已发送的数据后再进行发送
// =协程调度=
// * 在协程模式开启了 send_yield 情况下 send 遇到缓存区已满时会自动挂起，当数据被对端读走一部分后恢复协程，继续发送数据。
// =UnixSocket=
// * 监听 UnixSocket DGRAM 端口时，可以使用 send 向对端发送数据。
$server->on('packet', function (Swoole\Server $server, $data, $addr) {
    $server->send($addr['address'], 'SUCCESS', $addr['server_socket']);
});

// sendfile()
// 发送文件到 TCP 客户端连接。
// Swoole\Server->sendfile(int $fd, string $filename, int $offset = 0, int $length = 0): bool
// 此函数与 Server->send 都是向客户端发送数据，不同的是 sendfile 的数据来自于指定的文件

// sendto()
// 向任意的客户端 IP:PORT 发送 UDP 数据包。
// Swoole\Server->sendto(string $ip, int $port, string $data, int $serverSocket = -1): bool
// 服务器可能会同时监听多个 UDP 端口，参考多端口监听，此参数可以指定使用哪个端口发送数据包
// 必须监听了 UDP 的端口，才可以使用向 IPv4 地址发送数据
// 必须监听了 UDP6 的端口，才可以使用向 IPv6 地址发送数据

// sendwait()
// 同步地向客户端发送数据。
// Swoole\Server->sendwait(int $fd, string $data): bool
// =提示=
// 有一些特殊的场景，Server 需要连续向客户端发送数据，而 Server->send 数据发送接口是纯异步的，大量数据发送会导致内存发送队列塞满。
// 使用 Server->sendwait 就可以解决此问题，Server->sendwait 会等待连接可写。直到数据发送完毕才会返回。
// =注意=
// sendwait 目前仅可用于 SWOOLE_BASE 模式
// sendwait 只用于本机或内网通信，外网连接请勿使用 sendwait，在 enable_coroutine=>true (默认开启) 的时候也不要用这个函数，会卡死其他协程，只有同步阻塞的服务器才可以用。

// sendMessage()
// 向任意 Worker 进程或者 Task 进程发送消息。在非主进程和管理进程中可调用。收到消息的进程会触发 onPipeMessage 事件。
// Swoole\Server->sendMessage(string $message, int $workerId): bool
// =提示=
// 在 Worker 进程内调用 sendMessage 是异步 IO 的，消息会先存到缓冲区，可写时向 unixSocket 发送此消息
// 在 Task 进程 内调用 sendMessage 默认是同步 IO，但有些情况会自动转换成异步 IO，参考同步 IO 转换成异步 IO
// 在 User 进程 内调用 sendMessage 和 Task 一样，默认同步阻塞的，参考同步 IO 转换成异步 IO
// =注意=
// - 如果 sendMessage() 是异步 IO 的，如果对端进程因为种种原因不接收数据，千万不要一直 sendMessage()，会导致占用大量的内存资源，可以做个应答机制，对端不回应就不要发了；
// - MacOS/FreeBSD下超过 2K 就会使用临时文件存储；
// - 使用 sendMessage 必须注册 onPipeMessage 事件回调函数；
// - 设置了 task_ipc_mode = 3 将无法使用 sendMessage 向特定的 task 进程发送消息。
$server->on('pipeMessage', function ($server, $src_worker_id, $data) {
    echo "#{$server->worker_id} message from $src_worker_id: $data\n";
});
$server->on('task', function ($server, $task_id, $reactor_id, $data) {
    var_dump($task_id, $reactor_id, $data);
});
$server->on('finish', function ($server, $fd, $reactor_id) {

});
$server->on('receive', function (Swoole\Server $server, $fd, $reactor_id, $data) {
    $fd_info = $server->getClientInfo($fd);
    echo "客户端信息如下：\n";
    var_dump($fd_info);


    echo "遍历客户端链接：\n";
    $start_fd = 0;
    while (true) {
        $conn_list = $server->getClientList($start_fd, 10);
        if ($conn_list === false or count($conn_list) === 0) {
            echo "finish\n";
            break;
        }
        $start_fd = end($conn_list);
        var_dump($conn_list);
        foreach ($conn_list as $fd) {
            $server->send($fd, "broadcast");
        }
    }

    if (trim($data) == 'task') {
        $server->task('async task coming');
    } else {
        $worker_id = 1 - $server->worker_id;
        $server->sendMessage("hello task process", $worker_id);
    }
});

// exist()
// 检测 fd 对应的连接是否存在。
// Swoole\Server->exist(int $fd): bool

// pause()
// 停止接收数据。
// Swoole\Server->pause(int $fd)
// 调用此函数后会将连接从 EventLoop 中移除，不再接收客户端数据。
// 此函数不影响发送队列的处理
// 只能在 SWOOLE_PROCESS 模式下，调用 pause 后，可能有部分数据已经到达 Worker 进程，因此仍然可能会触发 onReceive 事件

// resume()
// 恢复数据接收。与 pause 方法成对使用。
// Swoole\Server->resume(int $fd)
// 调用此函数后会将连接重新加入到 EventLoop 中，继续接收客户端数据

// getClientInfo()
// 获取连接的信息，别名是 Swoole\Server->connection_info()
// Swoole\Server->getClientInfo(int $fd, int $extraData, bool $ignoreError = false): bool|array
// 客户端证书
// 仅在 onConnect 触发的进程中才能获取到证书
// 格式为 x509 格式，可使用 openssl_x509_parse 函数获取到证书信息
// 当使用 dispatch_mode = 1/3 配置时，考虑到这种数据包分发策略用于无状态服务，当连接断开后相关信息会直接从内存中删除，所以 Server->getClientInfo 是获取不到相关连接信息的。
// 返回 array 参数
// array(7) {
//   ["reactor_id"]=>
//   int(3)
//   ["server_fd"]=>
//   int(14)
//   ["server_port"]=>
//   int(9501)
//   ["remote_port"]=>
//   int(19889)
//   ["remote_ip"]=>
//   string(9) "127.0.0.1"
//   ["connect_time"]=>
//   int(1390212495)
//   ["last_time"]=>
//   int(1390212760)
// }
// reactor_id	来自哪个 Reactor 线程
// server_fd	来自哪个监听端口 socket，这里不是客户端连接的 fd
// server_port	来自哪个监听端口
// remote_port	客户端连接的端口
// remote_ip	客户端连接的 IP 地址
// connect_time	客户端连接到 Server 的时间，单位秒，由 master 进程设置
// last_time	最后一次收到数据的时间，单位秒，由 master 进程设置
// close_errno	连接关闭的错误码，如果连接异常关闭，close_errno 的值是非零，可以参考 Linux 错误信息列表
// websocket_status	[可选项] WebSocket 连接状态，当服务器是 Swoole\WebSocket\Server 时会额外增加此项信息
// uid	[可选项] 使用 bind 绑定了用户 ID 时会额外增加此项信息
// ssl_client_cert	[可选项] 使用 SSL 隧道加密，并且客户端设置了证书时会额外添加此项信息

// getClientList()
// 遍历当前 Server 所有的客户端连接，Server::getClientList 方法是基于共享内存的，不存在 IOWait，遍历的速度很快。
// 另外 getClientList 会返回所有 TCP 连接，而不仅仅是当前 Worker 进程的 TCP 连接。别名是 Swoole\Server->connection_list()
// Swoole\Server->getClientList(int $start_fd = 0, int $pageSize = 10): bool|array
// 返回值
// - 调用成功将返回一个数字索引数组，元素是取到的 $fd。数组会按从小到大排序。最后一个 $fd 作为新的 start_fd 再次尝试获取
// - 调用失败返回 false
// 提示
// - 推荐使用 Server::$connections 迭代器来遍历连接
// - getClientList 仅可用于 TCP 客户端，UDP 服务器需要自行保存客户端信息
// - SWOOLE_BASE 模式下只能获取当前进程的连接

// bind()
// 将连接绑定一个用户定义的 UID，可以设置 dispatch_mode=5 设置以此值进行 hash 固定分配。可以保证某一个 UID 的连接全部会分配到同一个 Worker 进程。
// Swoole\Server->bind(int $fd, int $uid)
// 提示
// - 可以使用 $serv->getClientInfo($fd) 查看连接所绑定 UID 的值
// - 在默认的 dispatch_mode=2 设置下，Server 会按照 socket fd 来分配连接数据到不同的 Worker 进程。因为 fd 是不稳定的，一个客户端断开后重新连接，fd 会发生改变。这样这个客户端的数据就会被分配到别的 Worker。使用 bind 之后就可以按照用户定义的 UID 进行分配。即使断线重连，相同 UID 的 TCP 连接数据会被分配相同的 Worker 进程。
// - 时序问题
//   - 客户端连接服务器后，连续发送多个包，可能会存在时序问题。在 bind 操作时，后续的包可能已经 dispatch，这些数据包仍然会按照 fd 取模分配到当前进程。只有在 bind 之后新收到的数据包才会按照 UID 取模分配。
//   - 因此如果要使用 bind 机制，网络通信协议需要设计握手步骤。客户端连接成功后，先发一个握手请求，之后客户端不要发任何包。在服务器 bind 完后，并回应之后。客户端再发送新的请求。
// - 重新绑定
//   - 某些情况下，业务逻辑需要用户连接重新绑定 UID。这时可以切断连接，重新建立 TCP 连接并握手，绑定到新的 UID。
// 注意
// - 仅在设置 dispatch_mode=5 时有效
// - 未绑定 UID 时默认使用 fd 取模进行分配
// - 同一个连接只能被 bind 一次，如果已经绑定了 UID，再次调用 bind 会返回 false
// 使用示例见文件 uid_dispatch_server.php 文件

// stats()
// 得到当前 Server 的活动 TCP 连接数，启动时间等信息，accpet/close(建立连接 / 关闭连接) 的总次数等信息。
// Swoole\Server->stats(): array
// array(12) {
//   ["start_time"]=>
//   int(1580610688)
//   ["connection_num"]=>
//   int(1)
//   ["accept_count"]=>
//   int(1)
//   ["close_count"]=>
//   int(0)
//   ["worker_num"]=>
//   int(1)
//   ["idle_worker_num"]=>
//   int(1)
//   ["tasking_num"]=>
//   int(0)
//   ["request_count"]=>
//   int(1)
//   ["worker_request_count"]=>
//   int(1)
//   ["worker_dispatch_count"]=>
//   int(1)
//   ["task_idle_worker_num"]=>
//   int(1)
//   ["coroutine_num"]=>
//   int(1)
// }
// start_time	服务器启动的时间
// connection_num	当前连接的数量
// accept_count	接受了多少个连接
// close_count	关闭的连接数量
// worker_num	开启了多少个 worker 进程
// idle_worker_num	空闲的 worker 进程数
// tasking_num	当前正在排队的任务数
// request_count	Server 收到的请求次数 【只有 onReceive、onMessage、onRequset、onPacket 四种数据请求计算 request_count】
// worker_request_count	当前 Worker 进程收到的请求次数【worker_request_count 超过 max_request 时工作进程将退出】
// worker_dispatch_count	master 进程向当前 Worker 进程投递任务的计数，在 master 进程进行 dispatch 时增加计数
// task_queue_num	消息队列中的 task 数量【用于 Task】
// task_queue_bytes	消息队列的内存占用字节数【用于 Task】
// task_idle_worker_num	空闲的 task 进程数量
// coroutine_num	当前协程数量 coroutine_num【用于 Coroutine】，想获取更多信息参考此节
$server->on('connect', function ($serv, $fd, $reactor_id) {
    echo "有客户端连接：".$fd;
    $stats = $serv->stats();
    print_r($stats);
});

// task()
// 投递一个异步任务到 task_worker 池中。此函数是非阻塞的，执行完毕会立即返回。Worker 进程可以继续处理新的请求。使用 Task 功能，必须先设置 task_worker_num，并且必须设置 Server 的 onTask 和 onFinish 事件回调函数。
// Swoole\Server->task(mixed $data, int $dstWorkerId  = -1): int
// =返回值=
// - 调用成功，返回值为整数 $task_id，表示此任务的 ID。如果有 finish 回应，onFinish 回调中会携带 $task_id 参数
// - 调用失败，返回值为 false，$task_id 可能为 0，因此必须使用 === 判断是否失败
// =提示=
// - 此功能用于将慢速的任务异步地去执行，比如一个聊天室服务器，可以用它来进行发送广播。当任务完成时，在 task 进程中调用 $serv->finish("finish") 告诉 worker 进程此任务已完成。当然 Swoole\Server->finish 是可选的。
// - task 底层使用 unixSocket 通信，是全内存的，没有 IO 消耗。单进程读写性能可达 100万/s，不同的进程使用不同的 unixSocket 通信，可以最大化利用多核。
// - 未指定目标 Task 进程，调用 task 方法会判断 Task 进程的忙闲状态，底层只会向处于空闲状态的 Task 进程投递任务。如果所有 Task 进程均处于忙的状态，底层会轮询投递任务到各个进程。可以使用 server->stats 方法获取当前正在排队的任务数量。
// - 第三个参数，可以直接设置 onFinish 函数，如果任务设置了回调函数，Task 返回结果时会直接执行指定的回调函数，不再执行 Server 的 onFinish 回调
// $server->task($data, -1, function (Swoole\Server $server, $task_id, $data) {
//     echo "Task Callback: ";
//     var_dump($task_id, $data);
// });
// - $task_id 是从 0-42 亿的整数，在当前进程内是唯一的
// - 默认不启动 task 功能，需要在手动设置 task_worker_num 来启动此功能
// - TaskWorker 的数量在 Server::set() 参数中调整，如 task_worker_num => 64，表示启动 64 个进程来接收异步任务
// =配置参数=
// - Server->task/taskwait/finish 3 个方法当传入的 $data 数据超过 8K 时会启用临时文件来保存。当临时文件内容超过 server->package_max_length 时底层会抛出一个警告。此警告不影响数据的投递，过大的 Task 可能会存在性能问题。
//       WARN: task package is too big.
// =单向任务=
// - 从 Master、Manager、UserProcess 进程中投递的任务，是单向的。在 TaskWorker 进程中无法使用 return 或 Server->finish() 方法返回结果数据。
// =注意=
// - task 方法不能在 task 进程 / 用户自定义进程中调用
// - 使用 task 必须为 Server 设置 onTask 和 onFinish 回调，否则 Server->start 会失败
// -task 操作的次数必须小于 onTask 处理速度，如果投递容量超过处理能力，task 数据会塞满缓存区，导致 Worker 进程发生阻塞。Worker 进程将无法接收新的请求
// - 使用 addProcess 添加的用户进程中无法使用 task 投递任务，请使用 sendMessage 接口与 Task 工作进程通信
// 详细使用见 task_deliver_server.php 文件

// taskwait()
// taskwait 与 task 方法作用相同，用于投递一个异步的任务到 task 进程池去执行。
// 与 task 不同的是 taskwait 是同步等待的，直到任务完成或者超时返回。$result 为任务执行的结果，
// 由 $server->finish 函数发出。如果此任务超时，这里会返回 false。
// Swoole\Server->taskwait(mixed $data, float $timeout = 0.5, int $dstWorkerId = -1): string|bool
// 提示
// - 协程模式
//   - 从 4.0.4 版本开始 taskwait 方法将支持协程调度，在协程中调用 Server->taskwait() 时将自动进行协程调度，不再阻塞等待。
//   - 借助协程调度器，taskwait 可以实现并发调用。
// - 同步模式
//   - 在同步阻塞模式下，taskwait 需要使用 UnixSocket 通信和共享内存，将数据返回给 Worker 进程，这个过程是同步阻塞的。
// - 特例
//   - 如果 onTask 中没有任何同步 IO 操作，底层仅有 2 次进程切换的开销，并不会产生 IO 等待，因此这种情况下 taskwait 可以视为非阻塞。实际测试 onTask 中仅读写 PHP 数组，进行 10 万次 taskwait 操作，总耗时仅为 1 秒，平均每次消耗为 10 微秒
// 注意
// - Swoole\Server::finish, 不要使用 taskwait
// - taskwait 方法不能在 task 进程中调用

// taskWaitMulti()
// 并发执行多个 task 异步任务，此方法不支持协程调度，会导致其他协程开始，协程环境下需要用下节的 taskCo。
// Swoole\Server->taskWaitMulti(array $tasks, float $timeout = 0.5): bool|array
// 返回值
// - 任务完成或超时，返回结果数组。结果数组中每个任务结果的顺序与 $tasks 对应，如：$tasks[2] 对应的结果为 $result[2]
// - 某个任务执行超时不会影响其他任务，返回的结果数据中将不包含超时的任务
// 注意
// - 最大并发任务不得超过 1024
// $tasks[] = mt_rand(1000, 9999); //任务1
// $tasks[] = mt_rand(1000, 9999); //任务2
// $tasks[] = mt_rand(1000, 9999); //任务3
// var_dump($tasks);

// //等待所有Task结果返回，超时为10s
// $results = $server->taskWaitMulti($tasks, 10.0);

// if (!isset($results[0])) {
//   echo "任务1执行超时了\n";
// }
// if (isset($results[1])) {
//   echo "任务2的执行结果为{$results[1]}\n";
// }
// if (isset($results[2])) {
//   echo "任务3的执行结果为{$results[2]}\n";
// }

// taskCo()
// 并发执行 Task 并进行协程调度，用于支持协程环境下的 taskWaitMulti 功能。
// Swoole\Server->taskCo(array $tasks, float $timeout = 0.5): array
// $tasks 任务列表，必须为数组。底层会遍历数组，将每个元素作为 task 投递到 Task 进程池
// $timeout 超时时间，默认为 0.5 秒，当规定的时间内任务没有全部完成，立即中止并返回结果
// 任务完成或超时，返回结果数组。结果数组中每个任务结果的顺序与 $tasks 对应，如：$tasks[2] 对应的结果为 $result[2]
// 某个任务执行失败或超时，对应的结果数组项为 false，如：$tasks[2] 失败了，那么 $result[2] 的值为 false
// 最大并发任务不得超过 1024
// 调度过程
// - $tasks 列表中的每个任务会随机投递到一个 Task 工作进程，投递完毕后，yield 让出当前协程，并设置一个 $timeout 秒的定时器
// - 在 onFinish 中收集对应的任务结果，保存到结果数组中。判断是否所有任务都返回了结果，如果为否，继续等待。如果为是，进行 resume 恢复对应协程的运行，并清除超时定时器
// - 在规定的时间内任务没有全部完成，定时器先触发，底层清除等待状态。将未完成的任务结果标记为 false，立即 resume 对应协程

// finish()
// 用于在 Task 进程中通知 Worker 进程，投递的任务已完成。此函数可以传递结果数据给 Worker 进程。
// Swoole\Server->finish(string $data)
// 提示
// - finish 方法可以连续多次调用，Worker 进程会多次触发 onFinish 事件
// - 在 onTask 回调函数中调用过 finish 方法后，return 数据依然会触发 onFinish 事件
// - Server->finish 是可选的。如果 Worker 进程不关心任务执行的结果，不需要调用此函数
// - 在 onTask 回调函数中 return 字符串，等同于调用 finish

$server->start();