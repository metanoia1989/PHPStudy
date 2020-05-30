<?php
// WebSocket Server 的常量、配置项以及其他相关对象

//********************************************************************* 
// WebSocket Server 的常量
//********************************************************************* 
// *数据帧类型*
// 常量	对应值	说明
// WEBSOCKET_OPCODE_TEXT	0x1	UTF-8 文本字符数据
// WEBSOCKET_OPCODE_BINARY	0x2	二进制数据
// WEBSOCKET_OPCODE_PING	0x9	ping 类型数据

// *连接状态*
// 常量	对应值	说明
// WEBSOCKET_STATUS_CONNECTION	1	连接进入等待握手
// WEBSOCKET_STATUS_HANDSHAKE	2	正在握手
// WEBSOCKET_STATUS_FRAME       3	已握手成功等待浏览器发送数据帧



//********************************************************************* 
// WebSocket Server 的选项
//********************************************************************* 
// WebSocket\Server 是 Server 的子类，可以使用 Server::set() 方法传入配置选项，设置某些参数。

// websocket_subprotocol
// 设置 WebSocket 子协议。
// 设置后握手响应的 HTTP 头会增加 Sec-WebSocket-Protocol: {$websocket_subprotocol}。具体使用方法请参考 WebSocket 协议相关 RFC 文档。
$server->set([
    'websocket_subprotocol' => 'chat',
]);

// open_websocket_close_frame
// 启用 websocket 协议中关闭帧（opcode 为 0x08 的帧）在 onMessage 回调中接收，默认为 false。
// 开启后，可在 Swoole\WebSocket\Server 中的 onMessage 回调中接收到客户端或服务端发送的关闭帧，开发者可自行对其进行处理。
$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
$server->set(array("open_websocket_close_frame" => true));
$server->on('open', function (Swoole\WebSocket\Server $server, $request) { });
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    if ($frame->opcode == 0x08) {
        echo "Close frame received: Code {$frame->code} Reason {$frame->reason}\n";
    } else {
        echo "Message received: {$frame->data}\n";
    }
});
$server->on('close', function ($ser, $fd) { });
$server->start();

// websocket_compression
// 启用数据压缩
// 为 true 时允许对帧进行 zlib 压缩，具体是否能够压缩取决于客户端是否能够处理压缩（根据握手信息决定，参见 RFC-7692） 
// 需要配合 flags 参数 SWOOLE_WEBSOCKET_FLAG_COMPRESS 来真正地对具体的某个帧进行压缩，具体使用方法见此节
// Swoole >= v4.4.12


//********************************************************************* 
// WebSocket Server 其他相关对象
//********************************************************************* 
// Swoole\WebSocket\Frame
// 在 v4.2.0 版本中，新增了服务端和客户端发送 Swoole\WebSocket\Frame 对象的支持
// 在 v4.4.12 版本中，新增了 flags 属性以支持 WebSocket 压缩帧，同时增加了一个新的子类 Swoole\WebSocket\CloseFrame
// 一个普通的 frame 对象具有以下属性
// object(Swoole\WebSocket\Frame)#1 (4) {
//   ["fd"]      =>  int(0)
//   ["data"]    =>  NULL
//   ["opcode"]  =>  int(1)
//   ["finish"]  =>  bool(true)
// }

// Swoole\WebSocket\CloseFrame
// 一个普通的 close frame 对象具有以下属性，多了 code 和 reason 属性，记录了关闭的错误代码和原因，code 可在 websocket 协议中定义的错误码 查询，reason 若是对端没有明确给出，则为空
// 如果服务端需要接收 close frame, 需要通过 $server->set 开启 open_websocket_close_frame 参数
// 在用于发送时，fd 属性会被忽略 (因为服务器端 fd 是第一个参数，客户端无需指定 fd), 所以 fd 是一个只读属性
// object(Swoole\WebSocket\CloseFrame)#1 (6) {
//   ["fd"]      =>  int(0)
//   ["data"]    =>  NULL
//   ["finish"]  =>  bool(true)
//   ["opcode"]  =>  int(8)
//   ["code"]    =>  int(1000)
//   ["reason"]  =>  string(0) ""
// }

// WebSocket 帧压缩 （RFC-7692）
// 首先你需要配置'websocket_compression' => true 来启用压缩（websocket 握手时将与对端交换压缩支持信息） 而后，
// 你可以使用 flag SWOOLE_WEBSOCKET_FLAG_COMPRESS 来对具体的某个帧进行压缩
// 服务端
$server = new Swoole\WebSocket\Server('127.0.0.1', 9501);
$server->set(['websocket_compression' => true]);
$server->on('message', function (Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame) {
  $server->push(
      $frame->fd,
      'Hello Swoole',
      SWOOLE_WEBSOCKET_OPCODE_TEXT,
      SWOOLE_WEBSOCKET_FLAG_FIN | SWOOLE_WEBSOCKET_FLAG_COMPRESS
  );
  // $server->push($frame->fd, $frame); // 或者 服务端可以直接原封不动转发客户端的帧对象
});
$server->start();

// 客户端
$cli = Swoole\Coroutine\Http\Client('127.0.0.1', 9501);
$cli->set(['websocket_compression' => true]);
$cli->upgrade('/');
$cli->push(
  'Hello Swoole',
  SWOOLE_WEBSOCKET_OPCODE_TEXT,
  SWOOLE_WEBSOCKET_FLAG_FIN | SWOOLE_WEBSOCKET_FLAG_COMPRESS
);