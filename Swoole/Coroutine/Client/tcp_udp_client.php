<?php
//*************************************************** 
// 协程 TCP/UDP 客户端
//*************************************************** 
// Coroutine\Client 提供了 TCP、UDP、unixSocket 传输协议的 Socket 客户端封装代码，使用时仅需 new Swoole\Coroutine\Client 即可。
// *实现原理*
// - Coroutine\Client 的所有涉及网络请求的方法，Swoole 都会进行协程调度，业务层无需感知
// - 使用方法和 Client 同步模式方法完全一致
// - connect 超时设置同时作用于 Connect 和 Recv、Send 超时
// *继承关系*
// - Coroutine\Client 与 Client 并不是继承关系，但 Client 提供的方法均可在 Coroutine\Client 中使用。请参考 Swoole\Client，在此不再赘述 。
// - 在 Coroutine\Client 中可以使用 set 方法设置配置选项，使用方法和与 Client->set 完全一致，对于使用有区别的函数，在 set() 函数小节会单独说明
Co\run(function(){
  $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
  if (!$client->connect('127.0.0.1', 9501, 0.5))
  {
      echo "connect failed. Error: {$client->errCode}\n";
  }
  $client->send("hello world\n");
  echo $client->recv();
  $client->close();
});
// *协议处理*
// 协程客户端也支持长度和 EOF 协议处理，设置方法与 Swoole\Client 完全一致。
$client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
$client->set(array(
    'open_length_check'     => 1,
    'package_length_type'   => 'N',
    'package_length_offset' => 0, //第N个字节是包长度的值
    'package_body_offset'   => 4, //第几个字节开始计算长度
    'package_max_length'    => 2000000, //协议最大长度
));

// connect()
// 连接到远程服务器。
// Swoole\Coroutine\Client->connect(string $host, int $port, float $timeout = 0.5): bool
// 提示
// - 如果连接失败，会返回 false
// - 超时后返回，检查 $cli->errCode 为 110
// - 失败重试， connect 连接失败后，不可直接进行重连。必须使用 close 关闭已有 socket，然后再进行 connect 重试。
if ($cli->connect('127.0.0.1', 9501) == false) {
    //关闭已有socket
    $cli->close();
    //重试
    $cli->connect('127.0.0.1', 9501);
}

// send()
// 发送数据。
// Swoole\Coroutine\Client->send(string $data): bool
// 发送成功返回写入 Socket 缓存区的字节数，底层会尽可能地将所有数据发出。如果返回的字节数与传入的 $data 长度不同，可能是 Socket 已被对端关闭，再下一次调用 send 或 recv 时将返回对应的错误码。
// 发送失败返回 false，可以使用 $client->errCode 获取错误原因。

// recv()
// recv 方法用于从服务器端接收数据。
// Swoole\Coroutine\Client->recv(float $timeout = -1): string
// 返回值
// - 设置了通信协议，recv 会返回完整的数据，长度受限于 package_max_length
// - 未设置通信协议，recv 最大返回 64K 数据
// - 未设置通信协议返回原始的数据，需要 PHP 代码中自行实现网络协议的处理
// - recv 返回空字符串表示服务端主动关闭连接，需要 close
// - recv 失败，返回 false，检测 $client->errCode 获取错误原因
// 超时设置
// - 传入了 $timeout，优先使用指定的 timeout 参数， 参考客户端超时规则
// - 未传入 $timeout，但在 connect 时指定了超时时间，自动以 connect 超时时间作为 recv 超时时间
// - 未传入 $timeout，未设置 connect 超时，将设置为 -1 表示永不超时
// - 发生超时的错误码为 ETIMEDOUT

// close()
// 关闭连接。
// close 不存在阻塞，会立即返回。关闭操作没有协程切换。
// Swoole\Coroutine\Client->close(): bool

// peek()
// 窥视数据。
// peek 方法直接操作 socket，因此不会引起协程调度。
// Swoole\Coroutine\Client->peek(int $length = 65535): string
// 提示
// peek 方法仅用于窥视内核 socket 缓存区中的数据，不进行偏移。使用 peek 之后，再调用 recv 仍然可以读取到这部分数据
// peek 方法是非阻塞的，它会立即返回。当 socket 缓存区中有数据时，会返回数据内容。缓存区为空时返回 false，并设置 $client->errCode
// 连接已被关闭 peek 会返回空字符串

// set()
// 设置客户端参数。
// Swoole\Coroutine\Client->set(array $settings): bool
// 配置参数
// 请参考 Swoole\Client 。
// 和 Swoole\Client 的差异
// 协程客户端提供了更细粒度的超时控制。可以设置：
// - timeout：总超时，包括连接、发送、接收所有超时
// - connect_timeout：连接超时
// - read_timeout：接收超时
// - write_timeout：发送超时
// 参考客户端超时规则
Co\run(function(){
    $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);

    $client->set(array(
        'timeout' => 0.5,
        'connect_timeout' => 1.0,
        'write_timeout' => 10.0,
        'read_timeout' => 0.5,
    ));

    if (!$client->connect('127.0.0.1', 9501, 0.5))
    {
        echo "connect failed. Error: {$client->errCode}\n";
    }
    $client->send("hello world\n");
    echo $client->recv();
    $client->close();
});