<?php
// TCP 服务器
// Swoole\Coroutine\Server 是一个完全协程化的类，用于创建协程 TCP 服务器，支持 TCP 和 unixSocket 类型。
// 与 Server 模块不同之处：
// - 动态创建销毁，在运行时可以动态监听端口，也可以动态关闭服务器
// - 处理连接的过程是完全同步的，程序可以顺序处理 Connect、Receive、Close 事件
// 在 4.4 以上版本中可用

// 短命名
// 可使用 Co\Server 短名。

//***********************************************************
// Swoole\Coroutine\Server 的方法
//***********************************************************

// __construct()
// 构造方法。
// Swoole\Coroutine\Server->__construct(string $host, int $port = 0, bool $ssl = false, bool $reuse_port);
// *$host 参数支持 3 种格式*
// - 0.0.0.0/127.0.0.1: IPv4 地址
// - ::/::1: IPv6 地址
// - unix:/tmp/test.sock: UnixSocket 地址
// *异常*
// - 参数错误、绑定地址和端口失败、listen 失败时将抛出 Swoole\Exception 异常。

// set()
// 设置协议处理参数。
// Swoole\Coroutine\Server->set(array $options);
// *配置参数*
// - 参数 $options 必须为一维的关联索引数组，与 setprotocol 方法接受的配置项完全一致。
// - 必须在 start() 方法之前设置参数
// 长度协议
$port = 9501; $ssl = false;
// $server = new Swoole\Coroutine\Server('0.0.0.0', $port, $ssl);
// $server->set([
//     'open_length_check' => true,
//     'package_max_length' => 1024 * 1024,
//     'package_length_type' => 'N',
//     'package_length_offset' => 0,
//     'package_body_offset' => 4,
// ]);
// SSL证书设置
// $server->set([
//     'ssl_cert_file' => dirname(__DIR__).'/ssl/server.crt',
//     'ssl_key_file' => dirname(__DIR__).'/ssl/server.key',
// ]);

// handle()
// 设置连接处理函数。
// 必须在 start() 之前设置处理函数
// Swoole\Coroutine\Server->handle(callable $fn);
// - 服务器在 Accept(建立连接) 成功后，会自动创建协程并执行 $fn ；
// - $fn 是在新的子协程空间内执行，因此在函数内无需再次创建协程；
// - $fn 接受一个参数，类型为 Swoole\Coroutine\Server\Connection 对象。
// - 此对象提供了三个方法：
//     1. recv()：接收数据，如果设置了协议处理，将每次返回完整的包
//     2. send($data)：发送数据
//     3. close()：关闭连接
// *回调函数*
// Socket 属性
// 可以读取 Connection::$socket 属性得到当前连接的 Socket 对象。可调用更多底层的方法，请参考 Swoole\Coroutine\Socket
function callback(Swoole\Coroutine\Server\Connection $conn) {
    while (true) {
        $data = $conn->recv();
    }
    $socket = $conn->socket;
    $socket->getpeername();
}

// shutdown()
// 终止服务器。
// 底层支持 start 和 shutdown 多次调用
// Swoole\Coroutine\Server->shutdown(): bool;

// start()
// 启动服务器。
// Swoole\Coroutine\Server->start(): bool;
// *返回值*
// - 启动失败会返回 false，并设置 errCode 属性
// - 启动成功将进入循环，Accept 连接
// - Accept(建立连接) 后会创建一个新的协程，并在协程中调用 handle 方法指定的函数
// *错误处理*
// - 当 Accept(建立连接) 发生 Too many open file 错误、或者无法创建子协程时，将暂停 1 秒然后再继续 Accept
// - 发生错误时，start () 方法将返回，错误信息将会以 Warning 的形式报出。

// 多进程管理模块
$pool = new Swoole\Process\Pool(2);
// 让每个OnWorkerStart回调都自动创建一个协程
$pool->set(['enable_coroutine' => true]);
$pool->on('workerStart', function ($pool, $id) {
    // 每个进程都监听9501端口
    $server = new Swoole\Coroutine\Server('0.0.0.0', 9501, false, true);
    // 收到15信号关闭服务 
    Swoole\Process::signal(SIGTERM, function () use ($server) {
        $server->shutdown();
    });
    // 接收到新的连接请求
    $server->handle(function (Swoole\Coroutine\Server\Connection $conn) {
        // 接收数据
        $data = $conn->recv();
        echo "来自客户端的数据：".json_encode($data);
        if (empty($data)) {
            // 关闭连接
            $conn->close();
        }
        // 发送数据
        $conn->send('hello');
    });
    // 开始监听端口
    $server->start();
});
$pool->start();

// =_= 上面的代码没太搞懂