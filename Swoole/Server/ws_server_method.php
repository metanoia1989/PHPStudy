<?php
// WebSocket\Server
// 通过内置的 WebSocket 服务器支持，通过几行 PHP 代码就可以写出一个异步 IO 的多进程的 WebSocket 服务器。
$server = new Swoole\WebSocket\Server('0.0.0.0', 9501);
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    echo "server: handshake success with fd {$request->fd}\n";
});

$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}: {$frame->date}, opcode: {$frame->opcode}, fin: {$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});


//********************************************************************* 
// WebSocket Server 事件
//********************************************************************* 
// WebSocket 服务器除了接收 Swoole\Server 和 Swoole\Http\Server 基类的回调函数外，额外增加了 3 个回调函数设置。其中：
// onMessage 回调函数为必选
// onOpen 和 onHandShake 回调函数为可选

// onRequest 回调
// WebSocket\Server 继承自 Http\Server，所以 Http\Server 提供的所有 API 和配置项都可以使用。请参考 Http\Server 章节。
// - 设置了 onRequest 回调，WebSocket\Server 也可以同时作为 http 服务器
// - 未设置 onRequest 回调，WebSocket\Server 收到 http 请求后会返回 http 400 错误页面
// - 如果想通过接收 http 触发所有 websocket 的推送，需要注意作用域的问题，面向过程请使用 global 对 WebSocket\Server 进行引用，面向对象可以把 WebSocket\Server 设置成一个成员属性
$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($server) {
    // global $server; // 调用外部的 server
    // 遍历所有websocket连接用户的fd，给所有用户推送
    foreach ($server->connections as $fd) {
        // 先判断是否是正确的websocket连接，否则有可能会push失败
        if ($server->isEstablished($fd)) {
            $server->push($fd, $request->get['message']);
        }
        $websocket_status = $server->connection_info($fd)['websocket_status'];
        $response->write("连接{$fd}是不是websocket：".$websocket_status."<br>");
    }
    $response->end("ouput end");
});
// *客户端*
// - Chrome/Firefox/ 高版本 IE/Safari 等浏览器内置了 JS 语言的 WebSocket 客户端
// - 微信小程序开发框架内置的 WebSocket 客户端
// - 异步 IO 的 PHP 程序中可以使用 Swoole\Coroutine\Http 作为 WebSocket 客户端
// - Apache/PHP-FPM 或其他同步阻塞的 PHP 程序中可以使用 swoole/framework 提供的同步 WebSocket 客户端
// - 非 WebSocket 客户端不能与 WebSocket 服务器通信
// *如何判断连接是否为 WebSocket 客户端*
// 通过使用 $server->connection_info($fd) 获取连接信息，返回的数组中有一项为 websocket_status，根据此状态可以判断是否为 WebSocket 客户端。
// websocket_status 连接状态
// 常量	对应值	说明
// WEBSOCKET_STATUS_CONNECTION	1	连接进入等待握手
// WEBSOCKET_STATUS_HANDSHAKE	2	正在握手
// WEBSOCKET_STATUS_FRAME	3	已握手成功等待浏览器发送数据帧

// onHandShake
// WebSocket 建立连接后进行握手。WebSocket 服务器会自动进行 handshake 握手的过程，如果用户希望自己进行握手处理，可以设置 onHandShake 事件回调函数。
// onHandShake(Swoole\Http\Request $request, Swoole\Http\Response $response);
// *提示*
// - onHandShake 事件回调是可选的
// - 设置 onHandShake 回调函数后不会再触发 onOpen 事件，需要应用代码自行处理
// - onHandShake 中必须调用 response->status() 设置状态码为 101 并调用 response->end() 响应，否则会握手失败.
// - 内置的握手协议为 Sec-WebSocket-Version: 13，低版本浏览器需要自行实现握手
// - 可以使用 server->defer 调用 onOpen 逻辑
// *注意*
// 如果您需要自行处理 handshake 的时候，再设置这个回调函数。如果您不需要 “自定义” 握手过程，那么不要设置该回调，用 Swoole 默认的握手即可。
// 下面是 “自定义”handshake 事件回调函数中必须要具备的：
$server->on('handshake', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        // print_r( $request->header );
        // if (如果不满足我某些自定义的需求条件，那么返回end输出，返回false，握手失败) {
        //    $response->end();
        //     return false;
        // }

        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );

        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();
});

// onOpen
// 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数。
// onOpen(Swoole\WebSocket\Server $server, Swoole\Http\Request $request);
// 提示
// - $request 是一个 HTTP 请求对象，包含了客户端发来的握手请求信息
// - onOpen 事件函数中可以调用 push 向客户端发送数据或者调用 close 关闭连接
// - onOpen 事件回调是可选的

// onMessage
// 当服务器收到来自客户端的数据帧时会回调此函数。
// onMessage(Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame)
// *提示*
// - $frame 是 Swoole\WebSocket\Frame 对象，包含了客户端发来的数据帧信息
// - onMessage 回调必须被设置，未设置服务器将无法启动
// - 客户端发送的 ping 帧不会触发 onMessage，底层会自动回复 pong 包
// *Swoole\WebSocket\Frame $frame*
// 属性	说明
// $frame->fd	客户端的 socket id，使用 $server->push 推送数据时需要用到
// $frame->data	数据内容，可以是文本内容也可以是二进制数据，可以通过 opcode 的值来判断
// $frame->opcode	WebSocket 的 OpCode 类型，可以参考 WebSocket 协议标准文档
// $frame->finish	表示数据帧是否完整，一个 WebSocket 请求可能会分成多个数据帧进行发送（底层已经实现了自动合并数据帧，现在不用担心接收到的数据帧不完整）
// $frame->data 如果是文本类型，编码格式必然是 UTF-8，这是 WebSocket 协议规定的
// *OpCode 与数据类型*
// OpCode	数据类型
// WEBSOCKET_OPCODE_TEXT = 0x1	文本数据
// WEBSOCKET_OPCODE_BINARY = 0x2	二进制数据


//********************************************************************* 
// WebSocket Server 方法
//********************************************************************* 
// push
// 向 websocket 客户端连接推送数据，长度最大不得超过 2M。
// Swoole\WebSocket\Server->push(int $fd, string $data, int $opcode = 1, bool $finish = true): bool;
// 自 v4.4.12 版本起，finish 参数（bool 型）改为 flags（int 型）以支持 WebSocket 压缩，finish 对应 SWOOLE_WEBSOCKET_FLAG_FIN 值为 1，
// 原有 bool 型值会隐式转换为 int 型，此改动向下兼容无影响。 此外压缩 flag 为 SWOOLE_WEBSOCKET_FLAG_COMPRESS。

// exist
// 判断 WebSocket 客户端是否存在，并且状态为 Active 状态。
// v4.3.0 以后，此 API 仅用于判断连接是否存在，请使用 isEstablished 判断是否为 websocket 连接
// Swoole\WebSocket\Server->exist(int $fd): bool;
// 返回值
// - 连接存在，并且已完成 WebSocket 握手，返回 true
// - 连接不存在或尚未完成握手，返回 false

// pack
// 打包 WebSocket 消息。
// Swoole\WebSocket\Server::pack(string $data, int $opcode = 1, bool $finish = true, bool $mask = false): string;
// 返回值
// - 返回打包好的 WebSocket 数据包，可通过 Swoole\Server 基类的 send () 发送给对端

// unpack
// 解析 WebSocket 数据帧。
// Swoole\WebSocket\Server::unpack(string $data): Swoole\WebSocket\Frame|false;

// disconnect
// 主动向 websocket 客户端发送关闭帧并关闭该连接。
// Swoole >= v4.0.3
// Swoole\WebSocket\Server->disconnect(int $fd, int $code = 1000, string $reason = ""): bool;
// 返回值
// 发送成功返回 true，发送失败或状态码非法时返回 false

// isEstablished
// 检查连接是否为有效的 WebSocket 客户端连接。
// 此函数与 exist 方法不同，exist 方法仅判断是否为 TCP 连接，无法判断是否为已完成握手的 WebSocket 客户端。
// Swoole\WebSocket\Server->isEstablished(int $fd): bool;

$server->start();