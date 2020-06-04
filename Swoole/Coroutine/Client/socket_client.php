<?php
//************************************************** 
// Coroutine\Socket
//************************************************** 
// Swoole\Coroutine\Socket 模块相比协程风格服务端和协程客户端相关模块 Socket 可以实现更细粒度的一些 IO 操作。
// 可使用 Co\Socket 短命名简化类名。此模块比较底层，使用者最好有 Socket 编程经验。

//************************************************** 
// 协程调度
//************************************************** 
// Coroutine\Socket 模块提供的 IO 操作接口均为同步编程风格，底层自动使用协程调度器实现异步 IO。


//************************************************** 
// 错误码
//************************************************** 
// 在执行 socket 相关系统调用时，可能返回 - 1 错误，底层会设置 Coroutine\Socket->errCode 属性为系统错误编号 errno，请参考响应的 man 文档。
// 如 $socket->accept() 返回错误时，errCode 含义可以参考 man accept 中列出的错误码文档。

//************************************************** 
// 属性
//************************************************** 
// fd
// socket 对应的文件描述符 ID

// errCode
// 错误码


//************************************************** 
// 方法
//************************************************** 
// __construct()
// 构造方法。构造 Coroutine\Socket 对象。
// Swoole\Coroutine\Socket->__construct(int $domain, int $type, int $protocol);
// 构造方法会调用 socket 系统调用创建一个 socket 句柄。调用失败时会抛出 Swoole\Coroutine\Socket\Exception 异常。
// 并设置 $socket->errCode 属性。可根据该属性的值得到系统调用失败的原因。

// getOption()
// 获取配置。
// 此方法对应 getsockopt 系统调用，详情可参见 man getsockopt 文档。
// 此方法和 sockets 扩展的 socket_get_option 功能等价，可以参见 PHP 文档。
// Swoole 版本 >= v4.3.2
// Swoole\Coroutine\Socket->getOption(int $level, int $optname): mixed


// setOption()
// 设置配置。
// 此方法对应 setsockopt 系统调用，详情可参见 man setsockopt 文档。此方法和 sockets 扩展的 socket_set_option 功能等价，可以参见 PHP 文档
// Swoole\Coroutine\Socket->setOption(int $level, int $optname, mixed $optval ): bool

// setProtocol()
// 使 socket 获得协议处理能力，可以配置是否开启 SSL 加密传输和解决 TCP 粘包问题 等
// Swoole 版本 >= v4.3.2
// Swoole\Coroutine\Socket->setProtocol(array $settings): bool
// 所有参数的意义和 Server::set() 完全一致，在此不再赘述。
$socket->setProtocol([
    'open_length_check'     => true,
    'package_max_length'    => 1024 * 1024,
    'package_length_type'   => 'N',
    'package_length_offset' => 0,
    'package_body_offset'   => 4,
]);

// bind()
// 绑定地址和端口。
// 此方法没有 IO 操作，不会引起协程切换
// Swoole\Coroutine\Socket->bind(string $address, int $port = 0): bool
// 返回值
// 绑定成功返回 true
// 绑定失败返回 false，请检查 errCode 属性获取失败原因

// listen()
// 监听 Socket。
// 此方法没有 IO 操作，不会引起协程切换
// Swoole\Coroutine\Socket->listen(int $backlog = 0): bool
// 如果应用中存在阻塞或耗时逻辑，accept 接受连接不及时，新创建的连接就会堆积在 backlog 监听队列中，如超出 backlog 长度，服务就会拒绝新的连接进入
// 返回值
// - 绑定成功返回 true
// - 绑定失败返回 false，请检查 errCode 属性获取失败原因
// backlog 的最大值受限于内核参数 net.core.somaxconn, 而 Linux 中可以工具 sysctl 来动态调整所有的 kernel 参数。动态调整是内核参数值修改后即时生效。但是这个生效仅限于 OS 层面，必须重启应用才能真正生效，命令 sysctl -a 会显示所有的内核参数及值。
// sysctl -w net.core.somaxconn=2048
// Copy to clipboardErrorCopied
// 以上命令将内核参数 net.core.somaxconn 的值改成了 2048。这样的改动虽然可以立即生效，但是重启机器后会恢复默认值。为了永久保留改动，需要修改 /etc/sysctl.conf，增加 net.core.somaxconn=2048 然后执行命令 sysctl -p 生效。

// accept()
// 接受客户端发起的连接。
// 调用此方法会立即挂起当前协程，并加入 EventLoop 监听可读事件，当 Socket 可读有到来的连接时自动唤醒该协程，并返回对应客户端连接的 Socket 对象。
// 该方法必须在使用 listen 方法后使用，适用于 Server 端。
// Swoole\Coroutine\Socket->accept(float $timeout = -1): Coroutine\Socket|false;
// 返回值
// - 超时或 accept 系统调用报错时返回 false，可使用 errCode 属性获取错误码，其中超时错误码为 ETIMEDOUT
// - 成功返回客户端连接的 socket，类型同样为 Swoole\Coroutine\Socket 对象。可对其执行 send、recv、close 等操作

// connect()
// 连接到目标服务器。
// 调用此方法会发起异步的 connect 系统调用，并挂起当前协程，底层会监听可写，当连接完成或失败后，恢复该协程。
// 该方法适用于 Client 端，支持 IPv4、IPv6、unixSocket。
// Swoole\Coroutine\Socket->connect(string $host, int $port = 0, float $timeout = -1): bool;
// 返回值
// 超时或 connect 系统调用报错时返回 false，可使用 errCode 属性获取错误码，其中超时错误码为 ETIMEDOUT
// 成功返回 true

// checkLiveness()
// 通过系统调用检查连接是否存活 (在异常断开时无效，仅能侦测到对端正常 close 下的连接断开)
// Swoole 版本 >= v4.5.0 可用
// Swoole\Coroutine\Socket->checkLiveness(): bool
// 返回值
// 连接存活时返回 true, 否则返回 false

// send()
// 向对端发送数据。
// send 方法会立即执行 send 系统调用发送数据，当 send 系统调用返回错误 EAGAIN 时，底层将自动监听可写事件，并挂起当前协程，等待可写事件触发时，重新执行 send 系统调用发送数据，并唤醒该协程。
// 如果 send 过快，recv 过慢最终会导致操作系统缓冲区写满，当前协程挂起在 send 方法，可以适当调大缓冲区，/proc/sys/net/core/wmem_max 和 SO_SNDBUF
// Swoole\Coroutine\Socket->send(string $data, float $timeout = -1): int|false;
// 返回值
// 发送成功返回写入的字节数，请注意实际写入的数据可能小于 $data 参数的长度，应用层代码需要对比返回值与 strlen($data) 是否相等来判断是否发送完成
// 发送失败返回 false，并设置 errCode 属性

// sendAll()
// 向对端发送数据。与 send 方法不同的是，sendAll 会尽可能完整地发送数据，直到成功发送全部数据或遇到错误中止。
// sendAll 方法会立即执行多次 send 系统调用发送数据，当 send 系统调用返回错误 EAGAIN 时，底层将自动监听可写事件，并挂起当前协程，等待可写事件触发时，重新执行 send 系统调用发送数据，直到数据发送完成或遇到错误，唤醒对应协程。
// Swoole 版本 >= v4.3.0
// Swoole\Coroutine\Socket->sendAll(string $data, float $timeout = -1) : int | false;
// 返回值
// - sendAll 会保证数据全部发送成功，但是 sendAll 期间对端有可能将连接断开，此时可能发送成功了部分数据，返回值会返回这个成功数据的长度，应用层代码需要对比返回值与 strlen($data) 是否相等来判断是否发送完成，根据业务需求是否需要续传。
// - 发送失败返回 false，并设置 errCode 属性

// peek()
// 窥视读缓冲区中的数据，相当于系统调用中的 recv(length, MSG_PEEK)。
// peek 是立即完成的，不会挂起协程，但有一次系统调用开销
// Swoole\Coroutine\Socket->peek(int $length = 65535): string|false;
// 返回值
// - 窥视成功返回数据
// - 窥视失败返回 false，并设置 errCode 属性

// recv()
// 接收数据。
// recv 方法会立即挂起当前协程并监听可读事件，等待对端发送数据后，可读事件触发时，执行 recv 系统调用获取 socket 缓存区中的数据，并唤醒该协程。
// Swoole\Coroutine\Socket->recv(int $length = 65535, float $timeout = -1): string|false;
// 返回值
// - 接收成功返回实际数据
// - 接收失败返回 false，并设置 errCode 属性
// - 接收超时，错误码为 ETIMEDOUT
// 返回值不一定等于预期长度，需要自行检查该次调用接收数据的长度，如需要保证单次调用获取到指定长度的数据，请使用 recvAll 方法或自行循环获取
// 粘包问题请参考 setProtocol() 方法，或者用 sendto();

// recvAll()
// 接收数据。与 recv 不同的是，recvAll 会尽可能完整地接收响应长度的数据，直到接收完成或遇到错误失败。
// recvAll 方法会立即挂起当前协程并监听可读事件，等待对端发送数据后，可读事件触发时，执行 recv 系统调用获取 socket 缓存区中的数据，重复该行为直到接收到指定长度的数据或遇到错误终止，并唤醒该协程。
// Swoole 版本 >= v4.3.0
// Swoole\Coroutine\Socket->recvAll(int $length = 65535, float $timeout = -1): string|false;
// 返回值
// - 接收成功返回实际数据，并且返回的字符串长度和参数长度一致
// - 接收失败返回 false，并设置 errCode 属性
// - 接收超时，错误码为 ETIMEDOUT

// recvPacket()
// 对于已通过 setProtocol 方法设置协议的 Socket 对象，可调用此方法接收一个完整的协议数据包
// Swoole 版本 >= v4.4.0
// Swoole\Coroutine\Socket->recvPacket(float $timeout = -1): string|false;
// 返回值
// - 接收成功返回一个完整协议数据包
// - 接收失败返回 false，并设置 errCode 属性
// - 接收超时，错误码为 ETIMEDOUT

// sendto()
// 向指定的地址和端口发送数据。用于 SOCK_DGRAM 类型的 socket。
// 此方法没有协程调度，底层会立即调用 sendto 向目标主机发送数据。此方法不会监听可写，sendto 可能会因为缓存区已满而返会 false，需要自行处理，或者使用 send 方法。
// Swoole\Coroutine\Socket->sendto(string $address, int $port, string $data): int|false;
// 返回值
// - 发送成功返回发送的字节数
// - 发送失败返回 false，并设置 errCode 属性
$socket = new Co\Socket(AF_INET, SOCK_DGRAM, 0);
$socket->sendto('127.0.0.1', 9601, "HELO");

// recvfrom()
// 接收数据，并设置来源主机的地址和端口。用于 SOCK_DGRAM 类型的 socket。
// 此方法会引起协程调度，底层会立即挂起当前协程，并监听可读事件。可读事件触发，收到数据后执行 recvfrom 系统调用获取数据包。
// Swoole\Coroutine\Socket->recvfrom(array &$peer, float $timeout = -1): string|false;
// 返回值
// - 成功接收数据，返回数据内容，并设置 $peer 为数组
// - 失败返回 false，并设置 errCode 属性，不修改 $peer 的内容
go(function () {
    $socket = new Co\Socket(AF_INET, SOCK_DGRAM, 0);
    $socket->bind('127.0.0.1', 9601);
    while (true) {
        $peer = null;
        $data = $socket->recvfrom($peer);
        echo "[Server] recvfrom[{$peer['address']}:{$peer['port']}] : $data\n";
        $socket->sendto($peer['address'], $peer['port'], "Swoole: $data");
    }
});

// getsockname()
// 获取 socket 的地址和端口信息。
// 此方法没有协程调度开销。
// Swoole\Coroutine\Socket->getsockname(): array|false;
// 返回值
// - 调用成功返回，包含 address 和 port 的数组
// - 调用失败返回 false，并设置 errCode 属性

// getpeername()
// 获取 socket 的对端地址和端口信息，仅用于 SOCK_STREAM 类型有连接的 socket。
// 此方法没有协程调度开销。
// Swoole\Coroutine\Socket->getpeername(): array|false;
// 返回值
// - 调用成功返回，包含 address 和 port 的数组
// - 调用失败返回 false，并设置 errCode 属性