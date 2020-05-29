<?php
// Swoole\Server 的配置
// Server::set() 函数用于设置 Server 运行时的各项参数。本节所有的子页面均为配置数组的元素。

// reactor_num
// 设置启动的 Reactor 线程数。【默认值：CPU 核数】
// 通过此参数来调节主进程内事件处理线程的数量，以充分利用多核。默认会启用 CPU 核数相同的数量。
// Reactor 线程是可以利用多核，如：机器有 128 核，那么底层会启动 128 线程。
// 每个线程能都会维持一个 EventLoop。线程之间是无锁的，指令可以被 128 核 CPU 并行执行。
// 考虑到操作系统调度存在一定程度的性能损失，可以设置为 CPU 核数 * 2，以便最大化利用 CPU 的每一个核。
// *提示*
// reactor_num 建议设置为 CPU 核数的 1-4 倍
// reactor_num 最大不得超过 swoole_cpu_num() * 4
// *注意*
// - reactor_num 必须小于或等于 worker_num ；
// - 如果设置的 reactor_num 大于 worker_num，会自动调整使 reactor_num 等于 worker_num ；
// - 在超过 8 核的机器上 reactor_num 默认设置为 8。


// worker_num
// 设置启动的 Worker 进程数。【默认值：CPU 核数】
// 如 1 个请求耗时 100ms，要提供 1000QPS 的处理能力，那必须配置 100 个进程或更多。
// 但开的进程越多，占用的内存就会大大增加，而且进程间切换的开销就会越来越大。所以这里适当即可。不要配置过大。
// *提示*
// - 如果业务代码是全异步 IO 的，这里设置为 CPU 核数的 1-4 倍最合理
// - 如果业务代码为同步 IO，需要根据请求响应时间和系统负载来调整，例如：100-500
// - 默认设置为 swoole_cpu_num()，最大不得超过 swoole_cpu_num() * 1000
// - 假设每个进程占用 40M 内存，100 个进程就需要占用 4G 内存，如何正确查看进程的内存占用请参考 Swoole 官方视频教程

// max_conn (max_connection)
// 服务器程序，最大允许的连接数。【默认值：ulimit -n】
// 如 max_connection => 10000, 此参数用来设置 Server 最大允许维持多少个 TCP 连接。超过此数量后，新进入的连接将被拒绝。
// *默认设置*
// - 应用层未设置 max_connection，底层将使用 ulimit -n 的值作为缺省设置
// - 在 4.2.9 或更高版本，当底层检测到 ulimit -n 超过 100000 时将默认设置为 100000，原因是某些系统设置了 ulimit -n 为 100万，需要分配大量内存，导致启动失败
// *最大上限*
// 请勿设置 max_connection 超过 1M
// *最小设置*
// - 此选项设置过小底层会抛出错误，并设置为 ulimit -n 的值。
// - 最小值为 (worker_num + task_worker_num) * 2 + 32
// serv->max_connection is too small.
// *内存占用*
// max_connection 参数不要调整的过大，根据机器内存的实际情况来设置。 Swoole 会根据此数值一次性分配一块大内存来保存 
// Connection 信息，一个 TCP 连接的 Connection 信息，需要占用 224 字节。
// *注意*
// max_connection 最大不得超过操作系统 ulimit -n 的值，否则会报一条警告信息，并重置为 ulimit -n 的值
// WARN    swServer_start_check: serv->max_conn is exceed the maximum value[100000].

// task_worker_num
// 配置 Task 进程的数量。【默认值：未配置则不启动 task】
// 配置此参数后将会启用 task 功能。所以 Server 务必要注册 onTask、onFinish 2 个事件回调函数。如果没有注册，服务器程序将无法启动。
// *提示*
// - Task 进程是同步阻塞的
// - 最大值不得超过 swoole_cpu_num() * 1000
// *计算方法*
// - 单个 task 的处理耗时，如 100ms，那一个进程 1 秒就可以处理 1/0.1=10 个 task
// - task 投递的速度，如每秒产生 2000 个 task
// - 2000/10=200，需要设置 task_worker_num => 200，启用 200 个 Task 进程
// *注意*
// - Task 进程内不能使用 Swoole\Server->task 方法

// task_ipc_mode
// 设置 Task 进程与 Worker 进程之间通信的方式。【默认值：1】
// 模式	作用
// 1	使用 Unix Socket 通信【默认模式】
// 2	使用 sysvmsg 消息队列通信
// 3	使用 sysvmsg 消息队列通信，并设置为争抢模式
// *模式 1*
// - 使用模式 1 时，支持定向投递，可在 task 和 taskwait 方法中使用 dst_worker_id，指定目标 Task进程。
// - dst_worker_id 设置为 -1 时，底层会判断每个 Task 进程的状态，向当前状态为空闲的进程投递任务。
// *模式 2、3*
// - 消息队列模式使用操作系统提供的内存队列存储数据，未指定 mssage_queue_key 消息队列 Key，将使用私有队列，在 Server 程序终止后会删除消息队列。
// - 指定消息队列 Key 后 Server 程序终止后，消息队列中的数据不会删除，因此进程重启后仍然能取到数据
// - 可使用 ipcrm -q 消息队列 ID 手工删除消息队列数据
// - 模式2 和模式3 的不同之处是，模式2 支持定向投递，$serv->task($data, $task_worker_id) 可以指定投递到哪个 task 进程。
// - 模式3 是完全争抢模式， task 进程会争抢队列，将无法使用定向投递，task/taskwait 将无法指定目标进程 ID，即使指定了 $task_worker_id，在模式3 下也是无效的。
// *注意*
// - 模式3 会影响 sendMessage 方法，使 sendMessage 发送的消息会随机被某一个 task 进程获取。
// - 使用消息队列通信，如果 Task进程 处理能力低于投递速度，可能会引起 Worker 进程阻塞。
// - 使用消息队列通信后 task 进程无法支持协程 (开启 task_enable_coroutine)。

// task_max_request
// 设置 task 进程的最大任务数。一个 task 进程在处理完超过此数值的任务后将自动退出。
// 这个参数是为了防止 PHP 进程内存溢出。如果不希望进程自动退出可以设置为 0。
// 设置 task 进程的最大任务数。【默认值：0】

// task_tmpdir
// 设置 task 的数据临时目录。【默认值：Linux /tmp 目录】
// 在 Server 中，如果投递的数据超过 8180 字节，将启用临时文件来保存数据。这里的 task_tmpdir 就是用来设置临时文件保存的位置。
// *提示*
// - 底层默认会使用 /tmp 目录存储 task 数据，如果你的 Linux 内核版本过低，/tmp 目录不是内存文件系统，可以设置为 /dev/shm/
// - Task_tmpdir 目录不存在，底层会尝试自动创建
// *注意*
// - 创建失败时，Server->start 会失败

// task_enable_coroutine
// 开启 Task 协程支持。【默认值：false】，v4.2.12 起支持
// 开启后自动在 onTask 回调中创建协程和协程容器，PHP 代码可以直接使用协程 API。
// 注意
// - task_enable_coroutine 必须在 enable_coroutine 为 true 时才可以使用
// - 开启 task_enable_coroutine，Task 工作进程支持协程
// - 未开启 task_enable_coroutine，仅支持同步阻塞
$server->on('Task', function ($serv, Swoole\Server\Task $task) {
    //来自哪个 Worker 进程
    $task->worker_id;
    //任务的编号
    $task->id;
    //任务的类型，taskwait, task, taskCo, taskWaitMulti 可能使用不同的 flags
    $task->flags;
    //任务的数据
    $task->data;
    //协程 API
    co::sleep(0.2);
    //完成任务，结束并返回数据
    $task->finish([123, 'hello']);
});

// task_use_object
// 使用面向对象风格的 Task 回调格式。【默认值：false】
// 设置为 true 时，onTask 回调将变成对象模式。
$server = new Swoole\Server('127.0.0.1', 9501);
$server->set([
    'worker_num'      => 1,
    'task_worker_num' => 3,
    'task_use_object' => true,
]);
$server->on('receive', function (Swoole\Server $server, $fd, $tid, $data) {
    $server->task(['fd' => $fd,]);
});
$server->on('Task', function (Swoole\Server $server, Swoole\Server\Task $task) {
    //此处$task是Swoole\Server\Task对象
    $server->send($task->data['fd'], json_encode($server->stats()));

});
$server->start();


// dispatch_mode
// 数据包分发策略。【默认值：2】
// 模式值	模式	作用
// 1	轮循模式	收到会轮循分配给每一个 Worker 进程
// 2	固定模式	根据连接的文件描述符分配 Worker。这样可以保证同一个连接发来的数据只会被同一个 Worker 处理
// 3	抢占模式	主进程会根据 Worker 的忙闲状态选择投递，只会投递给处于闲置状态的 Worker
// 4	IP 分配	根据客户端 IP 进行取模 hash，分配给一个固定的 Worker 进程。
// 可以保证同一个来源 IP 的连接数据总会被分配到同一个 Worker 进程。算法为 ip2long(ClientIP) % worker_num
// 5	UID 分配	需要用户代码中调用 Server->bind() 将一个连接绑定 1 个 uid。然后底层根据 UID 的值分配到不同的 Worker 进程。
// 算法为 UID % worker_num，如果需要使用字符串作为 UID，可以使用 crc32(UID_STRING)
// 7	stream 模式	空闲的 Worker 会 accept 连接，并接受 Reactor 的新请求
// *注意*
// - dispatch_mode=1/3 时，底层会屏蔽 onConnect/onClose 事件，原因是这 2 种模式下无法保证 onConnect/onClose/onReceive 的顺序；
// - 非请求响应式的服务器程序，请不要使用模式 1 或 3。例如：http 服务就是响应式的，可以使用 1 或 3，有 TCP 长连接状态的就不能使用 1 或 3。
// *使用建议*
// - 无状态 Server 可以使用 1 或 3，同步阻塞 Server 使用 3，异步非阻塞 Server 使用 1
// - 有状态使用 2、4、5
// *UDP 协议*
// - dispatch_mode=2/4/5 时为固定分配，底层使用客户端 IP 取模散列到不同的 Worker 进程，算法为 ip2long(ClientIP) % worker_num
// - dispatch_mode=1/3 时随机分配到不同的 Worker 进程
// *BASE 模式*
// - dispatch_mode 配置在 SWOOLE_BASE 模式是无效的，因为 BASE 不存在投递任务，
//   当收到客户端发来的数据后会立即在当前线程 / 进程回调 onReceive，不需要投递 Worker 进程。

// dispatch_func
// 设置 dispatch 函数，Swoole 底层内置了 6 种 dispatch_mode，如果仍然无法满足需求。可以使用编写 C++ 函数或 PHP 函数，实现 dispatch 逻辑。
$server->set(array(
  'dispatch_func' => 'my_dispatch_function',
));
// *提示*
// - 设置 dispatch_func 后底层会自动忽略 dispatch_mode 配置
// - dispatch_func 对应的函数不存在，底层将抛出致命错误
// - 如果需要 dispatch 一个超过 8K 的包，dispatch_func 只能获取到 0-8180 字节的内容
// *编写 PHP 函数*
// 由于 ZendVM 无法支持多线程环境，即使设置了多个 Reactor 线程，同一时间只能执行一个 dispatch_func。
// 因此底层在执行此 PHP 函数时会进行加锁操作，可能会存在锁的争抢问题。请勿在 dispatch_func 中执行任何阻塞操作，否则会导致 Reactor 线程组停止工作。
$server->set(array(
    'dispatch_func' => function ($server, $fd, $type, $data) {
        var_dump($fd, $type, $data);
        return intval($data[0]);
    },
));
// - $fd 为客户端连接的唯一标识符，可使用 Server::getClientInfo 获取连接信息
// - $type 数据的类型，0 表示来自客户端的数据发送，4 表示客户端连接建立，3 表示客户端连接关闭
// - $data 数据内容，需要注意：如果启用了 HTTP、EOF、Length 等协议处理参数后，底层会进行包的拼接。但在 dispatch_func 函数中只能传入数据包的前 8K 内容，不能得到完整的包内容。
// - 必须返回一个 0 - (server->worker_num - 1) 的数字，表示数据包投递的目标工作进程 ID
// - 小于 0 或大于等于 server->worker_num 为异常目标 ID，dispatch 的数据将会被丢弃
// *编写 C++ 函数*
// 在其他 PHP 扩展中，使用 swoole_add_function 注册长度函数到 Swoole 引擎中。
// C++ 函数调用时底层不会加锁，需要调用方自行保证线程安全性
// int dispatch_function(swServer *serv, swConnection *conn, swEventData *data);
// int dispatch_function(swServer *serv, swConnection *conn, swEventData *data)
// {
//     printf("cpp, type=%d, size=%d\n", data->info.type, data->info.len);
//     return data->info.len % serv->worker_num;
// }
// int register_dispatch_function(swModule *module)
// {
//     swoole_add_function("my_dispatch_function", (void *) dispatch_function);
// }
// - dispatch 函数必须返回投递的目标 worker 进程 id
// - 返回的 worker_id 不得超过 server->worker_num，否则底层会抛出段错误
// - 返回负数（return -1）表示丢弃此数据包
// - data 可以读取到事件的类型和长度
// - conn 是连接的信息，如果是 UDP 数据包，conn 为 NULL
// *注意*
// - dispatch_func 仅在 SWOOLE_PROCESS 模式下有效，UDP/TCP/UnixSocket 类型的服务器均有效
// - 返回的 worker_id 不得超过 server->worker_num，否则底层会抛出段错误

// message_queue_key
// 设置消息队列的 KEY【默认值：ftok($php_script_file, 1)】
// 仅在 task_ipc_mode = 2/3 时使用。设置的 Key 仅作为 Task 任务队列的 KEY，参考 Swoole 下的 IPC 通讯。
// task 队列在 server 结束后不会销毁，重新启动程序后， task 进程仍然会接着处理队列中的任务。如果不希望程序重新启动后执行旧的 Task 任务。可以手工删除此消息队列。
// ipcs -q 
// ipcrm -Q [msgkey]

// daemonize
// 守护进程化【默认值：0】
// 设置 daemonize => 1 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。
// 如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行。
// *提示*
// - 启用守护进程后，标准输入和输出会被重定向到 log_file
// - 如果未设置 log_file，将重定向到 /dev/null，所有打印屏幕的信息都会被丢弃
// - 启用守护进程后，CWD（当前目录）环境变量的值会发生变更，相对路径的文件读写会出错。PHP 程序中必须使用绝对路径
// - systemd
//   使用 systemd 或者 supervisord 管理 Swoole 服务时，请勿设置 daemonize = 1。
//   主要原因是 systemd 的机制与 init 不同。init 进程的 PID 为 1，
//   程序使用 daemonize 后，会脱离终端，最终被 init 进程托管，与 init 关系变为父子进程关系。
//   但 systemd 是启动了一个单独的后台进程，自行 fork 管理其他服务进程，因此不需要 daemonize，
//   反而使用了 daemonize = 1 会使得 Swoole 程序与该管理进程失去父子进程关系。

// backlog
// 设置 Listen 队列长度
// 如 backlog => 128，此参数将决定最多同时有多少个等待 accept 的连接。
// 我们知道 TCP 有三次握手的过程，客户端 syn=>服务端 syn+ack=>客户端 ack，当服务器收到客户端的 ack 后会将连接放到一个叫做 accept queue 的队列里面（注 1），
// 队列的大小由 backlog 参数和配置 somaxconn 的最小值决定，我们可以通过 ss -lt 命令查看最终的 accept queue 队列大小，Swoole 的主进程调用 accept（注 2）
// 从 accept queue 里面取走。 当 accept queue 满了之后连接有可能成功（注 4），
// 也有可能失败，失败后客户端的表现就是连接被重置（注 3）
// 或者连接超时，而服务端会记录失败的记录，可以通过 netstat -s|grep 'times the listen queue of a socket overflowed 来查看日志。如果出现了上述现象，你就应该调大该值了。 
// 幸运的是 Swoole 的 SWOOLE_PROCESS 模式与 PHP-FPM/Apache 等软件不同，并不依赖 backlog 来解决连接排队的问题。所以基本不会遇到上述现象。
// - 注 1:linux2.2 之后握手过程分为 syn queue 和 accept queue 两个队列，syn queue 长度由 tcp_max_syn_backlog 决定。
// - 注 2: 高版本内核调用的是 accept4，为了节省一次 set no block 系统调用。
// - 注 3: 客户端收到 syn+ack 包就认为连接成功了，实际上服务端还处于半连接状态，有可能发送 rst 包给客户端，客户端的表现就是 Connection reset by peer。
// - 注 4: 成功是通过 TCP 的重传机制，相关的配置有 tcp_synack_retries 和 tcp_abort_on_overflow。

// log_file
// 指定 Swoole 错误日志文件
// 在 Swoole 运行期发生的异常信息会记录到这个文件中，默认会打印到屏幕。
// 开启守护进程模式后 (daemonize => true)，标准输出将会被重定向到 log_file。
// 在 PHP 代码中 echo/var_dump/print 等打印到屏幕的内容会写入到 log_file 文件。
// *提示*
// log_file 中的日志仅仅是做运行时错误记录，没有长久存储的必要。
// *日志标号*
// 在日志信息中，进程 ID 前会加一些标号，表示日志产生的线程 / 进程类型。
// # Master 进程
// $ Manager 进程
// * Worker 进程
// ^ Task 进程
// *重新打开日志文件*
// 在服务器程序运行期间日志文件被 mv 移动或 unlink 删除后，日志信息将无法正常写入，这时可以向 Server 发送 SIGRTMIN 信号实现重新打开日志文件。
// - 仅支持 Linux 平台
// - 不支持 UserProcess 进程
// 注意
// - log_file 不会自动切分文件，所以需要定期清理此文件。观察 log_file 的输出，可以得到服务器的各类异常信息和警告。

// log_level
// 设置 Server 错误日志打印的等级，范围是 0-6。低于 log_level 设置的日志信息不会抛出。【默认值：SWOOLE_LOG_DEBUG】
// 常量	作用
// SWOOLE_LOG_DEBUG	调试日志，仅作为内核开发调试使用
// SWOOLE_LOG_TRACE	跟踪日志，可用于跟踪系统问题，调试日志是经过精心设置的，会携带关键性信息
// SWOOLE_LOG_INFO	普通信息，仅作为信息展示
// SWOOLE_LOG_NOTICE	提示信息，系统可能存在某些行为，如重启、关闭
// SWOOLE_LOG_WARNING	警告信息，系统可能存在某些问题
// SWOOLE_LOG_ERROR	错误信息，系统发生了某些关键性的错误，需要即时解决
// SWOOLE_LOG_NONE	相当于关闭日志信息，日志信息不会抛出
// SWOOLE_LOG_DEBUG 和 SWOOLE_LOG_TRACE 两种日志，必须在编译 swoole 扩展时使用 --enable-debug-log 或 --enable-trace-log 后才可以使用。
// 正常版本中即使设置了 log_level = SWOOLE_LOG_TRACE 也是无法打印此类日志的。
// 默认为 SWOOLE_LOG_DEBUG 也就是所有级别都打印；
// 在开启 daemonize 守护进程时，底层将把程序中的所有打印屏幕的输出内容写入到 log_file，这部分内容不受 log_level 控制。

// open_tcp_keepalive
// 在 TCP 中有一个 Keep-Alive 的机制可以检测死连接，应用层如果对于死链接周期不敏感或者没有实现心跳机制，可以使用操作系统提供的 keepalive 机制来踢掉死链接。 
// 在 Server::set() 配置中增加 open_tcp_keepalive=>1 表示启用 TCP keepalive。 另外，有 3 个选项可以对 keepalive 的细节进行调整，参考 Swoole 官方视频教程。
// *选项*
// - tcp_keepidle 单位秒，连接在 n 秒内没有数据请求，将开始对此连接进行探测。
// - tcp_keepcount 探测的次数，超过次数后将 close 此连接。
// - tcp_keepinterval 探测的间隔时间，单位秒。
$serv = new Swoole\Server("0.0.0.0", 6666, SWOOLE_PROCESS);
$serv->set(array(
    'worker_num' => 1,
    'open_tcp_keepalive' => 1,
    'tcp_keepidle' => 4, //4s没有数据传输就进行检测
    'tcp_keepinterval' => 1, //1s探测一次
    'tcp_keepcount' => 5, //探测的次数，超过5次后还没回包close此连接
));

$serv->on('connect', function ($serv, $fd) {
    var_dump("Client:Connect $fd");
});

$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
    var_dump($data);
});

$serv->on('close', function ($serv, $fd) {
  var_dump("close fd $fd");
});

$serv->start();


// heartbeat_check_interval
// 启用心跳检测【默认值：false】
// 此选项表示每隔多久轮循一次，单位为秒。如 heartbeat_check_interval => 60，表示每 60 秒，遍历所有连接，
// 如果该连接在 120 秒内（heartbeat_idle_time 未设置时默认为 interval 的两倍），没有向服务器发送任何数据，
// 此连接将被强制关闭。若未配置，则不会启用心跳，该配置默认关闭
// *提示*
// Server 并不会主动向客户端发送心跳包，而是被动等待客户端发送心跳。服务器端的 heartbeat_check 仅仅是检测连接上一次发送数据的时间， 
// 如果超过限制，将切断连接。 被心跳检测切断的连接依然会触发 onClose 事件回调
// *注意*
// heartbeat_check 仅支持 TCP 连接

// heartbeat_idle_time
// 连接最大允许空闲的时间
// 需要与 heartbeat_check_interval 配合使用
array(
    'heartbeat_idle_time'      => 600, // 表示一个连接如果600秒内未向服务器发送任何数据，此连接将被强制关闭
    'heartbeat_check_interval' => 60,  // 表示每60秒遍历一次
);
// *提示*
// - 启用 heartbeat_idle_time 后，服务器并不会主动向客户端发送数据包
// - 如果只设置了 heartbeat_idle_time 未设置 heartbeat_check_interval 底层将不会创建心跳检测线程，PHP 代码中可以调用 heartbeat 方法手工处理超时的连接

// open_eof_check
// 打开 EOF 检测【默认值：false】，参考 TCP 粘包问题
// 此选项将检测客户端连接发来的数据，当数据包结尾是指定的字符串时才会投递给 Worker 进程。否则会一直拼接数据包，直到超过缓存区或者超时才会中止。
// 当出错时底层会认为是恶意连接，丢弃数据并强制关闭连接。
// 常见的 Memcache/SMTP/POP 等协议都是以 \r\n 结束的，就可以使用此配置。开启后可以保证 Worker 进程一次性总是收到一个或者多个完整的数据包。
array(
    'open_eof_check' => true,   //打开EOF检测
    'package_eof'    => "\r\n", //设置EOF
);
// *注意*
// 此配置仅对 STREAM(流式的) 类型的 Socket 有效，如 TCP 、Unix Socket Stream
// EOF 检测不会从数据中间查找 eof 字符串，所以 Worker 进程可能会同时收到多个数据包，
// 需要在应用层代码中自行 explode("\r\n", $data) 来拆分数据包

// open_eof_split
// 启用 EOF 自动分包
// 当设置 open_eof_check 后，可能会产生多条数据合并在一个包内，open_eof_split 参数可以解决这个问题，参考 TCP 粘包问题。
// 设置此参数需要遍历整个数据包的内容，查找 EOF，因此会消耗大量 CPU 资源。假设每个数据包为 2M，每秒 10000 个请求，
// 这可能会产生 20G 条 CPU 字符匹配指令。
array(
    'open_eof_split' => true,   //打开EOF_SPLIT检测
    'package_eof'    => "\r\n", //设置EOF
);
// *提示*
// - 启用 open_eof_split 参数后，底层会从数据包中间查找 EOF，并拆分数据包。onReceive 每次仅收到一个以 EOF 字串结尾的数据包。
// - 启用 open_eof_split 参数后，无论参数 open_eof_check 是否设置，open_eof_split 都将生效。
// - 与 open_eof_check 的差异
//      open_eof_check 只检查接收数据的末尾是否为 EOF，因此它的性能最好，几乎没有消耗
//      open_eof_check 无法解决多个数据包合并的问题，比如同时发送两条带有 EOF 的数据，底层可能会一次全部返回
//      open_eof_split 会从左到右对数据进行逐字节对比，查找数据中的 EOF 进行分包，性能较差。但是每次只会返回一个数据包

// package_eof
// 设置 EOF 字符串。 参考 TCP 粘包问题
// 需要与 open_eof_check 或者 open_eof_split 配合使用。
// 注意
// package_eof 最大只允许传入 8 个字节的字符串

// open_length_check
// 打开包长检测特性【默认值：false】，参考 TCP 粘包问题
// 包长检测提供了固定包头 + 包体这种格式协议的解析。启用后，可以保证 Worker 进程 onReceive 每次都会收到一个完整的数据包。
// 长度检测协议，只需要计算一次长度，数据处理仅进行指针偏移，性能非常高，推荐使用。

// 省略一些不太认识的参数 
// ....
// package_length_type 长度值的类型
// package_length_func 设置长度解析函数
// package_max_length 设置最大数据包尺寸，单位为字节。

// open_http_protocol
// 启用 HTTP 协议处理。【默认值：false】
// 启用 HTTP 协议处理，Swoole\Http\Server 会自动启用此选项。设置为 false 表示关闭 HTTP 协议处理。

// open_mqtt_protocol
// 启用 mqtt 协议。【默认值：false】
// 启用后会解析 mqtt 包头，worker 进程 onReceive 每次会返回一个完整的 mqtt 数据包。
$server->set(array(
  'open_mqtt_protocol' => true
));

// open_websocket_protocol
// 启用 websocket 协议处理。【默认值：false】
// 启用 websocket 协议处理，Swoole\WebSocket\Server 会自动启用此选项。设置为 false 表示关闭 websocket 协议处理。
// 设置 open_websocket_protocol 选项为 true 后，会自动设置 open_http_protocol 协议也为 true。

// open_websocket_close_frame
// 启用 websocket 协议中关闭帧。【默认值：false】
// （opcode 为 0x08 的帧）在 onMessage 回调中接收
// 开启后，可在 WebSocketServer 中的 onMessage 回调中接收到客户端或服务端发送的关闭帧，开发者可自行对其进行处理。
$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
$server->set(array("open_websocket_close_frame" => true));
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {});
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    if ($frame->opcode == 0x08) {
        echo "Close frame received: Code {$frame->code} Reason {$frame->reason}\n";
    } else {
        echo "Message received: {$frame->data}\n";
    }
});
$server->on('close', function ($ser, $fd) {});
$server->start();

// open_tcp_nodelay
// 启用 open_tcp_nodelay【默认值：false】
// 开启后 TCP 连接发送数据时会关闭 Nagle 合并算法，立即发往对端 TCP 连接。在某些场景下，如命令行终端，
// 敲一个命令就需要立马发到服务器，可以提升响应速度，请自行 Google Nagle 算法。

// open_cpu_affinity
// 启用 CPU 亲和性设置。 【默认 false 关闭】
// 在多核的硬件平台中，启用此特性会将 Swoole 的 reactor线程 /worker进程绑定到固定的一个核上。
// 可以避免进程 / 线程的运行时在多个核之间互相切换，提高 CPU Cache 的命中率。
// 使用 taskset 命令查看进程的 CPU 亲和设置：
// taskset -p 进程ID
// pid 24666's current affinity mask: f
// pid 24901's current affinity mask: 8
// Copy to clipboardErrorCopied
// mask 是一个掩码数字，按 bit 计算每 bit 对应一个 CPU 核，如果某一位为 0 表示绑定此核，进程会被调度到此 CPU 上，为 0 表示进程不会被调度到此 CPU。
// 示例中 pid 为 24666 的进程 mask = f 表示未绑定到 CPU，操作系统会将此进程调度到任意一个 CPU 核上。 
// pid 为 24901 的进程 mask = 8，8 转为二进制是 1000，表示此进程绑定在第 4 个 CPU 核上。

// cpu_affinity_ignore
// IO 密集型程序中，所有网络中断都是用 CPU0 来处理，如果网络 IO 很重，CPU0 负载过高会导致网络中断无法及时处理，那网络收发包的能力就会下降。
// 如果不设置此选项，swoole 将会使用全部 CPU 核，底层根据 reactor_id 或 worker_id 与 CPU 核数取模来设置 CPU 绑定。
// 如果内核与网卡有多队列特性，网络中断会分布到多核，可以缓解网络中断的压力
array('cpu_affinity_ignore' => array(0, 1)); // 接受一个数组作为参数，array(0, 1) 表示不使用CPU0,CPU1，专门空出来处理网络中断。
// 注意
// 此选项必须与 open_cpu_affinity 同时设置才会生效

// tcp_defer_accept
// 启用 tcp_defer_accept 特性【默认值：false 关闭】
// 可以设置为一个数值，表示当一个 TCP 连接有数据发送时才触发 accept。
$server->set(array(
  'tcp_defer_accept' => 5
));
// 启用 tcp_defer_accept 特性后，accept 和 onConnect 对应的时间会发生变化。如果设置为 5 秒：
// - 客户端连接到服务器后不会立即触发 accept
// - 在 5 秒内客户端发送数据，此时会同时顺序触发 accept/onConnect/onReceive
// - 在 5 秒内客户端没有发送任何数据，此时会触发 accept/onConnect

// ssl_cert_file
// 设置 SSL 隧道加密。【默认值：无】
// 设置值为一个文件名字符串，指定 cert 证书和 key 私钥的路径。
// - PEM 转 DER 格式
//   openssl x509 -in cert.crt -outform der -out cert.der
// - DER 转 PEM 格式
//   openssl x509 -in cert.crt -inform der -outform pem -out cert.pem
// 注意
// - HTTPS 应用浏览器必须信任证书才能浏览网页；
// - wss 应用中，发起 WebSocket 连接的页面必须使用 HTTPS ；
// - 浏览器不信任 SSL 证书将无法使用 wss ；
// - 文件必须为 PEM 格式，不支持 DER 格式，可使用 openssl 工具进行转换。
// - 使用 SSL 必须在编译 Swoole 时加入 --enable-openssl 选项
$server = new Swoole\Server('0.0.0.0', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
$server->set(array(
    'ssl_cert_file' => __DIR__.'/config/ssl.crt',
    'ssl_key_file' => __DIR__.'/config/ssl.key',
));

// ssl_method
// 设置 OpenSSL 隧道加密的算法。【默认值：SWOOLE_SSLv23_METHOD】
// Server 与 Client 使用的算法必须一致，否则 SSL/TLS 握手会失败，连接会被切断
$server->set(array(
    'ssl_method' => SWOOLE_SSLv3_CLIENT_METHOD,
));
// SSL 加密方法
// 常量	作用
// SWOOLE_SSLv3_METHOD	-
// SWOOLE_SSLv3_SERVER_METHOD	-
// SWOOLE_SSLv3_CLIENT_METHOD	-
// SWOOLE_SSLv23_METHOD（默认加密方法）	-
// SWOOLE_SSLv23_SERVER_METHOD	-
// SWOOLE_SSLv23_CLIENT_METHOD	-
// SWOOLE_TLSv1_METHOD	-
// SWOOLE_TLSv1_SERVER_METHOD	-
// SWOOLE_TLSv1_CLIENT_METHOD	-
// SWOOLE_TLSv1_1_METHOD	-
// SWOOLE_TLSv1_1_SERVER_METHOD	-
// SWOOLE_TLSv1_1_CLIENT_METHOD	-
// SWOOLE_TLSv1_2_METHOD	-
// SWOOLE_TLSv1_2_SERVER_METHOD	-
// SWOOLE_TLSv1_2_CLIENT_METHOD	-
// SWOOLE_DTLSv1_METHOD	-
// SWOOLE_DTLSv1_SERVER_METHOD	-
// SWOOLE_DTLSv1_CLIENT_METHOD	-

// ssl_ciphers
// 设置 openssl 加密算法。【默认值：EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH】
// 提示
// ssl_ciphers 设置为空字符串时，由 openssl 自行选择加密算法
$server->set(array(
    'ssl_ciphers' => 'ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP',
));

// ssl_verify_peer
// 服务 SSL 设置验证对端证书。【默认值：false】
// 默认关闭，即不验证客户端证书。若开启，必须同时设置 ssl_client_cert_file 选项
// *提示*
// - ssl_verify_peer 开启验证对端证书功能，
// - ssl_allow_self_signed 允许自签名证书
// - ssl_client_cert_file 客户端证书
// *注意*
// - TCP 服务若验证失败，会底层会主动关闭连接。

// user
// 设置 Worker/TaskWorker 子进程的所属用户。【默认值：执行脚本用户】
// 服务器如果需要监听 1024 以下的端口，必须有 root 权限。但程序运行在 root 用户下，代码中一旦有漏洞，
// 攻击者就可以以 root 的方式执行远程指令， 风险很大。配置了 user 项之后，可以让主进程运行在 root 权限下，子进程运行在普通用户权限下。
// 注意
// - 仅在使用 root 用户启动时有效
// - 使用 user/group 配置项将工作进程设置为普通用户后，将无法在工作进程调用 shutdown/reload 方法关闭或重启服务。
//   只能使用 root 账户在 shell 终端执行 kill 命令。
$server->set(array(
  'user' => 'Apache'
));

// group
// 设置 worker/task 子进程的进程用户组。【默认值：执行脚本用户组】
// 与 user 配置相同，此配置是修改进程所属用户组，提升服务器程序的安全性。
// 仅在使用 root 用户启动时有效
$server->set(array(
  'group' => 'www-data'
));

// chroot
// 重定向 Worker 进程的文件系统根目录。【默认值：无】
// 此设置可以使进程对文件系统的读写与实际的操作系统文件系统隔离。提升安全性。
$server->set(array(
  'chroot' => '/data/server/'
));

// pid_file
// 设置 pid 文件地址。【默认值：无】
// 在 Server 启动时自动将 master 进程的 PID 写入到文件，在 Server 关闭时自动删除 PID 文件。
// 使用时需要注意如果 Server 非正常结束，PID 文件不会删除，需要使用 Swoole\Process::kill($pid, 0) 来侦测进程是否真的存在
$server->set(array(
    'pid_file' => __DIR__.'/server.pid',
));

// buffer_output_size
// 配置发送输出缓存区内存尺寸。【默认值：2M】
// 提示
// - 单位为字节，默认为 2M，如设置 32 * 1024 *1024 表示，单次 Server->send 最大允许发送 32M 字节的数据
// - 调用 Server->send， Http\Server->end/write，WebSocket\Server->push 等发送数据指令时，单次最大发送的数据不得超过 buffer_output_size 配置。
// 注意
// - 函数不应当调整过大，避免拥塞的数据过多，导致吃光机器内存
// - 开启大量 Worker 进程时，将会占用 worker_num * buffer_output_size 字节的内存
// 此参数只针对 SWOOLE_PROCESS 模式生效，因 PROCESS 模式下 worker 进程的数据要发送给主进程再发送给客户端， 所以每个 worker 进程会主进程开辟一块缓冲区。
$server->set([
    'buffer_output_size' => 32 * 1024 *1024, //必须为数字
]);

// enable_unsafe_event
// 启用 onConnect/onClose 事件。【默认值：false】
// Swoole 在配置 dispatch_mode=1 或 3 后，因为系统无法保证 onConnect/onReceive/onClose 的顺序，默认关闭了 onConnect/onClose 事件；
// 如果应用程序需要 onConnect/onClose 事件，并且能接受顺序问题可能带来的安全风险，可以通过设置 enable_unsafe_event 为 true，启用 onConnect/onClose 事件。

// discard_timeout_request
// 丢弃已关闭链接的数据请求。【默认值：true】
// Swoole 在配置 dispatch_mode=1 或 3 后，系统无法保证 onConnect/onReceive/onClose 的顺序，因此可能会有一些请求数据在连接关闭后，才能到达 Worker 进程。
// 提示
// - discard_timeout_request 配置默认为 true，表示如果 worker 进程收到了已关闭连接的数据请求，将自动丢弃。
// - discard_timeout_request 如果设置为 false，表示无论连接是否关闭 Worker 进程都会处理数据请求。

// enable_reuse_port
// 设置端口重用。【默认值：false】
// 启用端口重用后，可以重复启动监听同一个端口的 Server 程序
// 提示
// - enable_reuse_port = true 打开端口重用
// - enable_reuse_port = false 关闭端口重用
// 仅在 Linux-3.9.0 以上版本的内核可用 Swoole4.5 以上版本可用

// enable_delay_receive
// 设置 accept 客户端连接后将不会自动加入 EventLoop。【默认值：false】
// 设置此选项为 true 后，accept 客户端连接后将不会自动加入 EventLoop，仅触发 onConnect 回调。
// worker 进程可以调用 $server->confirm($fd) 对连接进行确认，此时才会将 fd 加入 EventLoop 开始进行数据收发，
// 也可以调用 $server->close($fd) 关闭此连接。
//开启enable_delay_receive选项
$server->set(array(
    'enable_delay_receive' => true,
));

$server->on("Connect", function ($server, $fd, $reactorId) {
    $server->after(2000, function() use ($server, $fd) {
        //确认连接，开始接收数据
        $server->confirm($fd);
    });
});

// reload_async
// 设置异步重启开关。【默认值：true】
// 设置异步重启开关。设置为 true 时，将启用异步安全重启特性，Worker 进程会等待异步事件完成后再退出。详细信息请参见 如何正确的重启服务
// reload_async 开启的主要目的是为了保证 服务重载时，协程 或 异步 任务能正常结束。
// *协程模式*
// 在 4.x 版本中开启 enable_coroutine 时，底层会额外增加一个协程数量的检测，当前无任何协程时进程才会退出，
// 开启时即使 reload_async => false 也会强制打开 reload_async。
$server->set([
  'reload_async' => true
]);

// max_wait_time
// 设置 Worker 进程收到停止服务通知后最大等待时间【默认值：3】
// 经常会碰到由于 worker 阻塞卡顿导致 worker 无法正常 reload, 无法满足一些生产场景，例如发布代码热更新需要 reload 进程。
// 所以，我们加入了进程重启超时时间的选项。
// 注意
// v4.4.x 以前默认为 30 秒
// 提示
// 管理进程收到重启、关闭信号后或者达到 max_request 时，管理进程会重起该 worker 进程。分以下几个步骤：
// - 底层会增加一个 (max_wait_time) 秒的定时器，触发定时器后，检查进程是否依然存在，如果是，会强制杀掉，重新拉一个进程。
// - 需要在 onWorkerStop 回调里面做收尾工作，需要在 max_wait_time 秒内做完收尾。
// - 依次向目标进程发送 SIGTERM 信号，杀掉进程。

// tcp_fastopen
// 开启 TCP 快速握手特性。【默认值：false】
// 此项特性，可以提升 TCP 短连接的响应速度，在客户端完成握手的第三步，发送 SYN 包时携带数据。
// 提示
// 此参数可以设置到监听端口上，想深入理解的同学可以查看 google 论文
$server->set([
  'tcp_fastopen' => true
]);

// request_slowlog_file
// 开启请求慢日志。【默认值：false】
// 由于这个慢日志的方案只能在同步阻塞的进程里面生效，不能在协程环境用，而 Swoole4 默认就是开启协程的，
// 除非关闭 enable_coroutine，所以不要使用了，使用 Swoole Tracker 的阻塞检测工具。
// 启用后 Manager 进程会设置一个时钟信号，定时侦测所有 Task 和 Worker 进程，一旦进程阻塞导致请求超过规定的时间，将自动打印进程的 PHP 函数调用栈。
// 底层基于 ptrace 系统调用实现，某些系统可能关闭了 ptrace，无法跟踪慢请求。请确认 kernel.yama.ptrace_scope 内核参数是否 0。
$server->set([
  'request_slowlog_file' => '/tmp/trace.log',
]);
// 注意
// 需要与 trace_event_worker 和 request_slowlog_timeout 配置项配合使用。
// 必须是具有可写权限的文件，否则创建文件失败底层会抛出致命错误；
// 默认仅监听 Task 进程，通过增加 trace_event_worker => true 来开启对 Worker 进程的跟踪
// 超时时间
$server->set([
  'request_slowlog_timeout' => 2, // 设置请求超时时间为2秒
  'request_slowlog_file' => '/tmp/trace.log',
  'trace_event_worker' => true, //跟踪 Task 和 Worker 进程
]);

// enable_coroutine
// 开启异步风格服务器的协程支持【默认值：On】
// enable_coroutine 关闭时在事件回调函数中不再自动创建协程，如果不需要用协程关闭这个会提高一些性能 参考什么是 Swoole 协程。
// 配置方法
// - 在 php.ini 配置 swoole.enable_coroutine = 'Off' (可见 ini 配置文档 )
// - $server->set(['enable_coroutine' => false]); 优先级高于 ini
// enable_coroutine 选项影响范围
// - onWorkerStart
// - onConnect
// - onOpen
// - onReceive
// - redis_onReceive
// - onPacket
// - onRequest
// - onMessage
// - onPipeMessage
// - onFinish
// - onClose
// - tick/after 定时器
// 开启 enable_coroutine 后在上述回调函数会自动创建协程
// - 当 enable_coroutine 设置为 true 时，底层自动在 onRequest 回调中创建协程，开发者无需自行使用 go 函数创建协程
// - 当 enable_coroutine 设置为 false 时，底层不会自动创建协程，开发者如果要使用协程，必须使用 go 自行创建协程，
//   如果不需要使用协程特性，则处理方式与 Swoole1.x 是 100% 一致的
$server = new Swoole\Http\Server("127.0.0.1", 9501);

$server->set([
    //关闭内置协程
    'enable_coroutine' => false,
]);
$server->on("request", function ($request, $response) {
    if ($request->server['request_uri'] == '/coro') {
        go(function () use ($response) {
            co::sleep(0.2);
            $response->header("Content-Type", "text/plain");
            $response->end("Hello World\n");
        });
    } else {
        $response->header("Content-Type", "text/plain");
        $response->end("Hello World\n");
    }
});
$server->start();

// max_coroutine
// 设置当前工作进程最大协程数量。【默认值：3000】
// 超过 max_coroutine 底层将无法创建新的协程，底层会抛出错误，并直接关闭连接。
// 在 Server 程序中实际最大可创建协程数量等于 worker_num * max_coroutine
$server->set(array(
    'max_coroutine' => 3000,
));

// send_yield
// 当发送数据时缓冲区内存不足时，直接在当前协程内 yield，等待数据发送完成，缓存区清空时，
// 自动 resume 当前协程，继续 send 数据。【默认值：在 dispatch_mod 2/4 时候可用，并默认开启】
// - Server/Client->send 返回 false 并且错误码为 SW_ERROR_OUTPUT_BUFFER_OVERFLOW 时，不返回 false 到 PHP 层，而是 yield 挂起当前协程
// - Server/Client 监听缓冲区是否清空的事件，在该事件触发后，缓存区内的数据已被发送完毕，这时 resume 对应的协程
// - 协程恢复后，继续调用 Server/Client->send 向缓存区内写入数据，这时因为缓存区已空，发送必然是成功的
for ($i = 0; $i < 100; $i++)
{
    //在缓存区塞满时会 yield 当前协程，发送完成后 resume 继续向下执行
    $server->send($fd, $data_2m);
}
// 此项特性会改变底层的默认行为，可以手动关闭
$server->set([
    'send_yield' => false,
]);
// 影响范围
// - Swoole\Server::send
// - Swoole\Http\Response::write
// - Swoole\WebSocket\Server::push
// - Swoole\Coroutine\Client::send
// - Swoole\Coroutine\Http\Client::push

// hook_flags
// 设置一键协程化 Hook 的函数范围。【默认值：不 hook】
// Swoole 版本为 v4.5+ 或 4.4LTS 可用，详情参考一键协程化 >= 可用
$server->set([
    'hook_flags' => SWOOLE_HOOK_SLEEP,
]);