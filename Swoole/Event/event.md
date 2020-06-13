# Event
Swoole 扩展还提供了直接操作底层 epoll/kqueue 事件循环的接口。可将其他扩展创建的 socket，PHP 代码中 stream/socket 扩展创建的 socket 等加入到 Swoole 的 EventLoop 中， 否则第三方的 `$fd` 如果是同步 IO 会导致 Swoole 的 EventLoop 得不到执行，参考案例。       

同步 IO 转换成异步 IO  https://wiki.swoole.com/#/learn?id=%e5%90%8c%e6%ad%a5io%e8%bd%ac%e6%8d%a2%e6%88%90%e5%bc%82%e6%ad%a5io

Event 模块比较底层，是 epoll 的初级封装，使用者最好有 IO 多路复用编程经验。     


# 事件优先级
1. 通过 `Process::signal` 设置的信号处理回调函数
2. 通过 `Timer::tick` 和 `Timer::after` 设置的定时器回调函数
3. 通过 `Event::defer` 设置的延迟执行函数
4. 通过 `Event::cycle` 设置的周期回调函数

# 方法
## add()
将一个 socket 加入到底层的 reactor 事件监听中。此函数可以用在 Server 或 Client 模式下。
```php
Swoole\Event::add(mixed $sock, callable $read_callback, callable $write_callback = null, int $flags = null): bool;
```

在 Server 程序中使用时，必须在 Worker 进程启动后使用。在 Server::start 之前不得调用任何异步 IO 接口

**参数**
`mixed $sock` 文件描述符、stream 资源、sockets 资源、object     
`callable $read_callback` 可读事件回调函数      
`callable $write_callback` 为可写事件回调函数【此参数可以是字符串函数名、对象 + 方法、类静态方法或匿名函数，当此 socket 可读或者可写时回调指定的函数。】        
`int $flags` 事件类型的掩码【可选择关闭 / 开启可读可写事件，如 SWOOLE_EVENT_READ、SWOOLE_EVENT_WRITE 或者 SWOOLE_EVENT_READ|SWOOLE_EVENT_WRITE】

**$sock 4 种类型**
类型    |	说明
---|---
int     | 文件描述符，包括 `Swoole\Client->$sock`、`Swoole\Process->$pipe` 或者其他 fd
stream  | 资源	`stream_socket_client/fsockopen` 创建的资源
sockets | 资源	sockets 扩展中 `socket_create` 创建的资源，需要在编译时加入 `./configure --enable-sockets`
object  | Swoole\Process 或 Swoole\Client，底层自动转换为 UnixSocket（Process）或客户端连接的 socket（Swoole\Client）


**返回值**
- 添加事件监听成功成功返回 true
- 添加失败返回 false，请使用 swoole_last_error 获取错误码
- 已添加过的 socket 不能重复添加，可以使用 swoole_event_set 修改 socket 对应的回调函数和事件类型

使用 Swoole\Event::add 将 socket 加入到事件监听后，底层会自动将该 socket 设置为非阻塞模式

使用示例
```php
$fp = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
fwrite($fp,"GET / HTTP/1.1\r\nHost: www.qq.com\r\n\r\n");

Swoole\Event::add($fp, function($fp) {
    $resp = fread($fp, 8192);
    //socket处理完成后，从epoll事件中移除socket
    Swoole\Event::del($fp);
    fclose($fp);
});
echo "Finish\n";  //Swoole\Event::add 不会阻塞进程，这行代码会顺序执行
```

**回调函数**
- 在可读 (`$read_callback`) 事件回调函数中必须使用 fread、recv 等函数读取 socket 缓存区中的数据，否则事件会持续触发，如果不希望继续读取必须使用 Swoole\Event::del 移除事件监听
- 在可写 ($write_callback) 事件回调函数中，写入 socket 之后必须调用 Swoole\Event::del 移除事件监听，否则可写事件会持续触发
- 执行 fread、socekt_recv、socket_read、Swoole\Client::recv 返回 false，并且错误码为 EAGAIN 时表示当前 socket 接收缓存区内没有任何数据，这时需要加入可读监听等待 EventLoop 通知
- 执行 fwrite、socket_write、socket_send、Swoole\Client::send 操作返回 false，并且错误码为 EAGAIN 时表示当前 socket 发送缓存区已满，暂时不能发送数据。需要监听可写事件等待 EventLoop 通知

## set()
修改事件监听的回调函数和掩码。
```php
Swoole\Event::set($fd, mixed $read_callback, mixed $write_callback, int $flags): bool;
```
**参数**
参数与 Event::add 完全相同。如果传入 `$fd` 在 EventLoop 中不存在返回 false。        
当 `$read_callback` 不为 null 时，将修改可读事件回调函数为指定的函数        
当 `$write_callback` 不为 null 时，将修改可写事件回调函数为指定的函数       
`$flags` 可关闭 / 开启，可写（SWOOLE_EVENT_READ）和可读（SWOOLE_EVENT_WRITE）事件的监听     
注意如果监听了 `SWOOLE_EVENT_READ` 事件，而当前并未设置 read_callback，底层会直接返回 false，添加失败。`SWOOLE_EVENT_WRITE` 同理。      

**状态变更**
使用 Event::add 或 Event::set 设置了可读事件回调，但并未监听 SWOOLE_EVENT_READ 可读事件，这时底层仅保存回调函数的信息，并不会产生任何事件回调。     
可以使用 `Event::set($fd, null, null, SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE)`，修改监听的事件类型，这时底层会触发可读事件。      

**释放回调函数**
注意 Event::set 只能替换回调函数，但并不能释放事件回调函数。如：Event::set($fd, null, null, SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE)，参数中传入的 read_callback 和 write_callback 为 null，表示不对 Event::add 设置的回调函数进行修改，而不是将事件回调函数设为 null。

只有调用 Event::del 清除事件监听时，底层才会释放 read_callback 和 write_callback 事件回调函数。

## isset()
检测传入的 `$fd` 是否已加入了事件监听。
```php
Swoole\Event::isset(mixed $fd, int $events = SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE): bool;
```

**$events**
事件类型    |	说明
---|---
SWOOLE_EVENT_READ   |	是否监听了可读事件
SWOOLE_EVENT_WRITE  |	是否监听了可写事件
SWOOLE_EVENT_READ `|` SWOOLE_EVENT_WRITE    |	监听可读或可写事件

**使用示例**
```php
use Swoole\Event;

$fp = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
fwrite($fp,"GET / HTTP/1.1\r\nHost: www.qq.com\r\n\r\n");

Event::add($fp, function($fp) {
    $resp = fread($fp, 8192);
    Swoole\Event::del($fp);
    fclose($fp);
}, null, SWOOLE_EVENT_READ);
var_dump(Event::isset($fp, SWOOLE_EVENT_READ)); //返回 true
var_dump(Event::isset($fp, SWOOLE_EVENT_WRITE)); //返回 false
var_dump(Event::isset($fp, SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE)); //返回 true
```

## write()
用于 PHP 自带 stream/sockets 扩展创建的 socket，使用 fwrite/socket_send 等函数向对端发送数据。当发送的数据量较大，socket 写缓存区已满，就会发送阻塞等待或者返回 EAGAIN 错误。

Event::write 函数可以将 stream/sockets 资源的数据发送变成异步的，当缓冲区满了或者返回 EAGAIN，Swoole 底层会将数据加入到发送队列，并监听可写。socket 可写时 Swoole 底层会自动写入
```php
Swoole\Event::write(mixed $fd, miexd $data): bool;
```

**参数**
`mixed $fd`
功能：任意的 socket 文件描述符【参考 Event::add 文档】
默认值：无
其它值：无

`miexd $data`

功能：要发送的数据 【发送数据的长度不得超过 Socket 缓存区尺寸】
默认值：无
其它值：无
Event::write 不能用于 SSL/TLS 等有隧道加密的 stream/sockets 资源
Event::write 操作成功后，会自动将该 $socket 设置为非阻塞模式

**使用示例**
```php
use Swoole\Event;

$fp = stream_socket_client('tcp://127.0.0.1:9501');
$data = str_repeat('A', 1024 * 1024*2);

Event::add($fp, function($fp) {
     echo fread($fp);
});

Event::write($fp, $data);
```

**SOCKET 缓存区已满后，Swoole 的底层逻辑**
持续写入 SOCKET 如果对端读取不够快，那 SOCKET 缓存区会塞满。Swoole 底层会将数据存到内存缓存区中，直到可写事件触发再写入 SOCKET。        
如果内存缓存区也被写满了，此时 Swoole 底层会抛出 pipe buffer overflow, reactor will block. 错误，并进入阻塞等待。       

缓存塞满返回 false 是原子操作，只会出现全部写入成功或者全部失败

## del()
从 reactor 中移除监听的 socket。 Event::del 应当与 Event::add 成对使用。
```php
Swoole\Event::del(mixed $sock): bool;
```

必须在 socket 的 close 操作前使用 Event::del 移除事件监听，否则可能会产生内存泄漏

参数

`mixed $sock`
功能：socket 的文件描述符

## exit()
退出事件轮询。

此函数仅在 Client 程序中有效
```php
Swoole\Event::exit(): void;
```

## defer()
在下一个事件循环开始时执行函数。
```php
Swoole\Event::defer(mixed $callback_function);
```

Event::defer 的回调函数会在当前 EventLoop 的事件循环结束、下一次事件循环开始前执行。

使用示例
```php
Swoole\Event::defer(function(){
    echo "After EventLoop\n";
});
```

## cycle()
定义事件循环周期执行函数。此函数会在每一轮事件循环结束时调用。
```php
Swoole\Event::cycle(callable $callback, bool $before = false): bool;
```

使用示例
```php
Swoole\Timer::tick(2000, function ($id) {
    var_dump($id);
});

Swoole\Event::cycle(function () {
    echo "hello [1]\n";
    Swoole\Event::cycle(function () {
        echo "hello [2]\n";
        Swoole\Event::cycle(null);
    });
});
```

## wait()
启动事件监听。
请将此函数放置于 PHP 程序末尾
```php
Swoole\Event::wait();
```
使用示例
```php
Swoole\Timer::tick(1000, function () {
    echo "hello\n";
});

Swoole\Event::wait();
```

## dispatch()
启动事件监听。

仅执行一次 reactor->wait 操作，在 Linux 平台下相当手工调用一次 epoll_wait。与 Event::dispatch 不同的是，Event::wait 在底层内部维持了循环。
```php
Swoole\Event::dispatch();
```

使用示例
```php
while(true)
{
    Event::dispatch();
}
```

此函数的目的是兼容一些框架，如 amp，它在框架内部自行控制 reactor 的循环，而使用 Event::wait，Swoole 底层维持了控制权，就无法让出给框架方。