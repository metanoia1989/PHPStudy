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
$server->on('connect', function ($server, $fd) {
    echo "Client: Conenct.\n";
});

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
$server->on('receive', function ($serv, $fd, $reactor_id, $data) use ($process) {
    // 定时向客户端发送消息
    $serv->tick(1000, function () use ($serv, $fd) {
        $serv->send($fd, "hello client，this is from server random number: ".rand(1, 100)."\n");
    });

    // 群接收到的消息
    $socket = $process->exportSocket();
    $socket->send($data);

});

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
        $server->tick(1000, function () {
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


$server->start();