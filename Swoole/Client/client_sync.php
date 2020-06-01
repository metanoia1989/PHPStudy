<?php
//******************************************************* 
// Swoole\Client 同步阻塞客户端
//******************************************************* 
// Swoole\Client 以下简称 Client，提供了 TCP/UDP、socket 的客户端的封装代码，使用时仅需 new Swoole\Client 即可。可用于 FPM/Apache 环境。
// 相对传统的 streams 系列函数，有几大优势：
// - stream 函数存在超时设置的陷阱和 Bug，一旦没处理好会导致 Server 端长时间阻塞
// - stream 函数的 fread 默认最大 8192 长度限制，无法支持 UDP 的大包
// - Client 支持 waitall，在有确定包长度时可一次取完，不必循环读取
// - Client 支持 UDP Connect，解决了 UDP 串包问题
// - Client 是纯 C 的代码，专门处理 socket，stream 函数非常复杂。Client 性能更好
// - Client 支持长连接
// - 可以使用 swoole_client_select 函数实现多个 Client 的并发控制
$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send('hello world\n');
echo $client->recv();
$client->close();


//******************************************************* 
// Swoole\Client 常量
//******************************************************* 
// SWOOLE_KEEP
// Swoole\Client 支持在 PHP-FPM/Apache 中创建一个 TCP 长连接到服务器端。
// 启用 SWOOLE_KEEP 选项后，一个请求结束不会关闭 socket，下一次再进行 connect 时会自动复用上次创建的连接。
// 如果执行 connect 发现连接已经被服务器关闭，那么 connect 会创建新的连接。
// *SWOOLE_KEEP 的优势*
// - TCP 长连接可以减少 connect 3 次握手 /close 4 次挥手带来的额外 IO 消耗
// - 降低服务器端 close/connect 次数
$client = new Swoole\Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
$client->connect('127.0.0.1', 9501);

// Client::MSG_WAITALL
// - 如果设定了 Client::MSG_WAITALL 参数就必须设定准确的 $size，否则会一直等待，直到接收的数据长度达到 $size
// - 未设置 Client::MSG_WAITALL 时，$size 最大为 64K
// - 如果设置了错误的 $size，会导致 recv 超时，返回 false

// Client::MSG_DONTWAIT
// 非阻塞接收数据，无论是否有数据都会立即返回。

// Client::MSG_PEEK
// 窥视 socket 缓存区中的数据。设置 MSG_PEEK 参数后，recv 读取数据不会修改指针，因此下一次调用 recv 仍然会从上一次的位置起返回数据。

// Client::MSG_OOB
// 读取带外数据，请自行搜索 "TCP带外数据 "。


//******************************************************* 
// Swoole\Client 配置
//******************************************************* 
// Client 可以使用 set 方法设置一些选项，启用某些特性。

// 协议解析
// 为了解决 TCP 粘包问题，相关的配置的意义和 Swoole\Server 一致，详情请移步到 Swoole\Server 协议配置章节。
// - 目前支持 open_length_check 和 open_eof_check 2 种自动协议处理功能
// - 配置好了协议解析后，客户端的 recv() 方法将不接受长度参数，每次必然返回一个完整的数据包
// *配置示例*
// - 结束符检测
$client->set(array(
    'open_eof_check' => true,
    'package_eof' => "\r\n\r\n",
    'package_max_length' => 1024 * 1024 * 2,
));
// - 长度检测
$client->set(array(
    'open_length_check' => 1,
    'package_length_type' => 'N',
    'package_length_offset' => 0, //第N个字节是包长度的值
    'package_body_offset' => 4, //第几个字节开始计算长度
    'package_max_length' => 2000000, //协议最大长度
));
// - MQTT 协议 启用 MQTT 协议解析，onReceive 回调将收到完整的 MQTT 数据包。
$client->set(array(
    'open_mqtt_protocol' => true,
));
// - Socket 缓存区尺寸 包括 socket 底层操作系统缓存区、应用层接收数据内存缓存区、应用层发送数据内存缓冲区。
// - 关闭 Nagle 合并算法
$client->set(array(
  'open_tcp_nodelay' => true,
));

// SSL 相关
// - SSL/TLS 证书配置
$client->set(array(
    'ssl_cert_file' => $your_ssl_cert_file_path,
    'ssl_key_file' => $your_ssl_key_file_path,
));
// - ssl_verify_peer 验证服务器端证书。
//   启用后会验证证书和主机域名是否对应，如果为否将自动关闭连接
$client->set([
    'ssl_verify_peer' => true,
]);
// - 自签名证书 
//   可设置 ssl_allow_self_signed 为 true，允许自签名证书。
$client->set([
    'ssl_verify_peer' => true,
    'ssl_allow_self_signed' => true,
]);
// - ssl_host_name
//   设置服务器主机名称，与 ssl_verify_peer 配置配合使用或 Client::verifyPeerCert 配合使用。
$client->set([
    'ssl_host_name' => 'www.google.com',
]);
// - ssl_cafile
//   当设置 ssl_verify_peer 为 true 时， 用来验证远端证书所用到的 CA 证书。 本选项值为 CA 证书在本地文件系统的全路径及文件名。
$client->set([
    'ssl_cafile' => '/etc/CA',
]);
// - ssl_capath
//   如果未设置 ssl_cafile，或者 ssl_cafile 所指的文件不存在时， 会在 ssl_capath 所指定的目录搜索适用的证书。 
//   该目录必须是已经经过哈希处理的证书目录。
$client->set([
    'ssl_capath' => '/etc/capath/',
]);

// package_length_func
// 设置长度计算函数，与 Swoole\Server 的 package_length_func 使用方法完全一致。与 open_length_check 配合使用。长度函数必须返回一个整数。
// - 返回 0，数据不足，需要接收更多数据
// - 返回 -1，数据错误，底层会自动关闭连接
// - 返回包的总长度值（包括包头和包体的总长度），底层会自动将包拼好后返回给回调函数
// 默认底层最大会读取 8K 的数据，如果包头的长度较小可能会存在内存复制的消耗。可设置 package_body_offset 参数，底层只读取包头进行长度解析。
$client->set(array(
    'open_length_check' => true,
    'package_length_func' => function ($data) {
        if (strlen($data) < 8) {
            return 0;
        }
        $length = intval(trim(substr($data, 0, 8)));
        if ($length <= 0) {
            return -1;
        }
        return $length + 8;
    },
));

// socks5_proxy
// 配置 socks5 代理。
// 仅设置一个选项是无效的，每次必须设置 host 和 port；socks5_username、socks5_password 为可选参数。
$client->set(array(
    'socks5_host' => '192.168.1.100',
    'socks5_port' => 1080,
    'socks5_username' => 'username',
    'socks5_password' => 'password',
));

// http_proxy
// 配置 HTTP 代理。
$client->set(array(
    'http_proxy_host' => '192.168.1.100',
    'http_proxy_port' => 1080,
    'http_proxy_user' => 'test', // 可选
    'http_proxy_password' => 'test_123456', // 可选
));

// bind
// 仅设置 bind_port 是无效的，请同时设置 bind_port 和 bind_address
// 机器有多个网卡的情况下，设置 bind_address 参数可以强制客户端 Socket 绑定某个网络地址。
// 设置 bind_port 可以使客户端 Socket 使用固定的端口连接到外网服务器。
$client->set(array(
    'bind_address' => '192.168.1.100',
    'bind_port' => 36002,
));

// 作用范围
// 以上 Client 配置项对下面这些客户端同样生效
// Swoole\Coroutine\Client
// Swoole\Coroutine\Http\Client
// Swoole\Coroutine\Http2\Client

//******************************************************* 
// Swoole\Client 方法
//******************************************************* 
// __construct()
// 构造方法
// Swoole\Client->__construct(int $sock_type, int $is_sync = SWOOLE_SOCK_SYNC, string $key);
// - int $sock_type socket 的类型 支持 SWOOLE_SOCK_TCP、SWOOLE_SOCK_TCP6、SWOOLE_SOCK_UDP、SWOOLE_SOCK_UDP6
// - int $is_sync 同步阻塞模式，现在只有这一个类型，保留此参数只为了兼容 api 默认值：SWOOLE_SOCK_SYNC
// - string $key 用于长连接的 Key【默认使用 IP:PORT 作为 key。相同的 keynew 两次也只用一个 TCP 连接】
// *在 PHP-FPM/Apache 中创建长连接*
// $cli = new Swoole\Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
// 加入 SWOOLE_KEEP 标志后，创建的 TCP 连接在 PHP 请求结束或者调用 $cli->close() 时并不会关闭。下一次执行 connect 调用时会复用上一次创建的连接。
// 长连接保存的方式默认是以 ServerHost:ServerPort 为 key 的。可以再第 3 个参数内指定 key。
// Client 对象析构会自动调用 close 方法关闭 socket
// *在 Server 中使用 Client*
// - 必须在事件回调函数中使用 Client。
// - Server 可以用任何语言编写的 socket client 来连接。同样 Client 也可以去连接任何语言编写的 socket server
// - 在 Swoole4+ 协程环境下使用此 Client 会导致退步为同步模型。

// set()
// 设置客户端参数，必须在 connect 前执行。
// Swoole\Client->set(array $settings);

// connect()
// 连接到远程服务器。
// Swoole\Client->connect(string $host, int $port, float $timeout = 0.5, int $flag = 0): bool
// int $flag
// 在 UDP 类型时表示是否启用 udp_connect 设定此选项后将绑定 $host 与 $port，此 UDP 将会丢弃非指定 host/port 的数据包。
// 在 TCP 类型，$flag=1 表示设置为非阻塞 socket，之后此 fd 会变成异步 IO，connect 会立即返回。
// 如果将 $flag 设置为 1，那么在 send/recv 前必须使用 swoole_client_select 来检测是否完成了连接。
// *返回值*
// - 成功返回 true
// - 失败返回 false，请检查 errCode 属性获取失败原因
// *同步模式*
// - connect 方法会阻塞，直到连接成功并返回 true。这时候就可以向服务器端发送数据或者收取数据了。
// - 同步 TCP 客户端在执行 close 后，可以再次发起 Connect 创建新连接到服务器
// *失败重连*
// - connect 失败后如果希望重连一次，必须先进行 close 关闭旧的 socket，否则会返回 EINPROCESS 错误，因为当前的 socket 正在连接服务器，客户端并不知道是否连接成功，所以无法再次执行 connect。调用 close 会关闭当前的 socket，底层重新创建新的 socket 来进行连接。
// - 启用 SWOOLE_KEEP 长连接后，close 调用的第一个参数要设置为 true 表示强行销毁长连接 socket
if ($socket->connect('127.0.0.1', 9502) === false) {
    $socket->close(true);
    $socket->connect('127.0.0.1', 9502);
}
// *UDP Connect*
// 默认底层并不会启用 udp connect，一个 UDP 客户端执行 connect 时，底层在创建 socket 后会立即返回成功。这时此 socket 绑定的地址是 0.0.0.0，任何其他对端均可向此端口发送数据包。
// 如 $client->connect('192.168.1.100', 9502)，这时操作系统为客户端 socket 随机分配了一个端口 58232，其他机器，如 192.168.1.101 也可以向这个端口发送数据包。
// 未开启 udp connect，调用 getsockname 返回的 host 项为 0.0.0.0
// 将第 4 项参数设置为 1，启用 udp connect，$client->connect('192.168.1.100', 9502, 1, 1)。这时将会绑定客户端和服务器端，底层会根据服务器端的地址来绑定 socket 绑定的地址。
// 如连接了 192.168.1.100，当前 socket 会被绑定到 192.168.1.* 的本机地址上。启用 udp connect 后，客户端将不再接收其他主机向此端口发送的数据包。

// isConnected()
// Swoole\Client->isConnected(): bool;
// 返回 Client 的连接状态
// - 返回 false，表示当前未连接到服务器
// - 返回 true，表示当前已连接到服务器
// isConnected 方法返回的是应用层状态，只表示 Client 执行了 connect 并成功连接到了 Server，并且没有执行 close 关闭连接。Client 可以执行 send、recv、close 等操作，但不能再次执行 connect 。
// 这不代表连接一定是可用的，当执行 send 或 recv 时仍然有可能返回错误，因为应用层无法获得底层 TCP 连接的状态，执行 send 或 recv 时应用层与内核发生交互，才能得到真实的连接可用状态。

// getSocket()
// 获取底层的 socket 句柄，返回的对象为 sockets 资源句柄。
// 此方法需要依赖 sockets 扩展，并且编译时需要开启 --enable-sockets 选项
// Swoole\Client->getSocket();
// 使用 socket_set_option 函数可以设置更底层的一些 socket 参数。
$socket = $client->getSocket();
if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
    echo 'Unable to set option on socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
}

// getSockName()
// 用于获取客户端 socket 的本地 host:port。
// 必须在连接之后才可以使用
// Swoole\Client->getsockname(): array|false;
// 返回值
// array('host' => '127.0.0.1', 'port' => 53652);

// getPeerName()
// 获取对端 socket 的 IP 地址和端口
// 仅支持 SWOOLE_SOCK_UDP/SWOOLE_SOCK_UDP6/SWOOLE_SOCK_UNIX_DGRAM 类型
// Swoole\Client->getpeername(): array|false;
// Copy to clipboardErrorCopied
// UDP 协议通信客户端向一台服务器发送数据包后，可能并非由此服务器向客户端发送响应。可以使用 getpeername 方法获取实际响应的服务器 IP:PORT。
// 此函数必须在 $client->recv() 之后调用

// getPeerCert()
// 获取服务器端证书信息。
// Swoole\Client->getPeerCert(): string|false;
// 返回值
// - 成功返回一个 X509 证书字符串信息
// - 失败返回 false
// 必须在 SSL 握手完成后才可以调用此方法。
// 可以使用 openssl 扩展提供的 openssl_x509_parse 函数解析证书的信息。 !> 需要在编译 swoole 时启用 --enable-openssl

// verifyPeerCert()
// 验证服务器端证书。
// Swoole\Client->verifyPeerCert();

// send()
// 发送数据到远程服务器，必须在建立连接后，才可向对端发送数据。
// Swoole\Client->send(string $data): int|false;
// 返回值
// - 成功发送，返回已发数据长度
// - 失败返回 false，并设置 errCode 属性
// 提示
// - 如果未执行 connect，调用 send 会触发警告
// - 发送的数据没有长度限制
// - 发送的数据太大 Socket 缓存区塞满，程序会阻塞等待可写

// sendto()
// 向任意 IP:PORT 的主机发送 UDP 数据包，仅支持 SWOOLE_SOCK_UDP/SWOOLE_SOCK_UDP6 类型
// Swoole\Client->sendto(string $ip, int $port, string $data): bool;

// sendfile()
// 发送文件到服务器，本函数是基于 sendfile 操作系统调用实现
// Swoole\Client->sendfile(string $filename, int $offset = 0, int $length = 0): bool;
// sendfile 不能用于 UDP 客户端和 SSL 隧道加密连接
// 返回值
// - 如果传入的文件不存在，将返回 false
// - 执行成功返回 true
// 注意
// - sendfile 会一直阻塞直到整个文件发送完毕或者发生致命错误

// recv()
// 从服务器端接收数据。
// Swoole\Client->recv(int $size = 65535, int $flags = 0): string | false
// 返回值
// - 成功收到数据返回字符串
// - 连接关闭返回空字符串
// - 失败返回 false，并设置 $client->errCode 属性
// EOF/Length 协议
// - 客户端启用了 EOF/Length 检测后，无需设置 $size 和 $waitall 参数。扩展层会返回完整的数据包或者返回 false，参考协议解析章节)。
// - 当收到错误的包头或包头中长度值超过 package_max_length 设置时，recv 会返回空字符串，PHP 代码中应当关闭此连接。

// close()
// 关闭连接。
// Swoole\Client->close(bool $force = false): bool
// 当一个 swoole_client 连接被 close 后不要再次发起 connect。正确的做法是销毁当前的 Client，重新创建一个 Client 并发起新的连接。
// Client 对象在析构时会自动 close。

// enableSSL()
// 动态开启 SSL 隧道加密。
// Swoole\Client->enableSSL(): bool
// 客户端在建立连接时使用明文通信，中途希望改为 SSL 隧道加密通信，可以使用 enableSSL 方法来实现。如果一开始就是 SSL 的请参考参考 SSL 配置。
// 使用 enableSSL 动态开启 SSL 隧道加密，需要满足两个条件：
// - 客户端创建时类型必须为非 SSL
// - 客户端已与服务器建立了连接
// 调用 enableSSL 会阻塞等待 SSL 握手完成。

// swoole_client_select
// Swoole\Client 的并行处理中用了 select 系统调用来做 IO 事件循环，不是 epoll_wait，与 Event 模块不同的是，此函数是用在同步 IO 环境中的 (如果在 Swoole 的 Worker 进程中调用，会导致 Swoole 自己的 epollIO 事件循环没有机会执行)。
// 函数原型：
// int swoole_client_select(array &$read, array &$write, array &$error, float $timeout);
// - swoole_client_select 接受 4 个参数，$read, $write, $error 分别是可读 / 可写 / 错误的文件描述符。
// - 这 3 个参数必须是数组变量的引用。数组的元素必须为swoole_client 对象。
// - 此方法基于 select 系统调用，最大支持 1024 个 socket
// - $timeout 参数是 select 系统调用的超时时间，单位为秒，接受浮点数
// - 功能与 PHP 原生的 stream_select() 类似，不同的是 stream_select 只支持 PHP 的 stream 变量类型，而且性能差。
// 调用成功后，会返回事件的数量，并修改 $read/$write/$error 数组。使用 foreach 遍历数组，然后执行 $item->recv/$item->send 来收发数据。
// 或者调用 $item->close() 或 unset($item) 来关闭 socket。
// swoole_client_select 返回 0 表示在规定的时间内，没有任何 IO 可用，select 调用已超时。
// 此函数可以用于 Apache/PHP-fpm 环境

//******************************************************* 
// Swoole\Client 属性
//******************************************************* 
// errCode
// 错误码
// Swoole\Client->errCode: int;
// 当 connect/send/recv/close 失败时，会自动设置 $swoole_client->errCode 的值。
// errCode 的值等于 Linux errno。可使用 socket_strerror 将错误码转为错误信息。
// echo socket_strerror($client->errCode);

// sock
// socket 连接的文件描述符。
// Swoole\Client->sock;
// 在 PHP 代码中可以使用
// $sock = fopen("php://fd/".$swoole_client->sock); 
// - 将 Swoole\Client 的 socket 转换成一个 stream socket。可以调用 fread/fwrite/fclose 等函数进程操作。
// - Swoole\Server 中的 $fd 不能用此方法转换，因为 $fd 只是一个数字，$fd 文件描述符属于主进程，参考 SWOOLE_PROCESS 模式。
// - $swoole_client->sock 可以转换成 int 作为数组的 key。
// - 这里需要注意的是：$swoole_client->sock 属性值，仅在 $swoole_client->connect 后才能取到。在未连接服务器之前，此属性的值为 null。

// reuse
// 表示此连接是新创建的还是复用已存在的。与 SWOOLE_KEEP 配合使用。
// 使用场景
// WebSocket 客户端与服务器建立连接后需要进行握手，如果连接是复用的，那就不需要再次进行握手，直接发送 WebSocket 数据帧即可。
if ($client->reuse) {
    $client->send($data);
} else {
    $client->doHandShake();
    $client->send($data);
}

