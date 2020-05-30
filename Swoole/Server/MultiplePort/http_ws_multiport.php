<?php
// Http/WebSocket
// Swoole\Http\Server 和 Swoole\WebSocket\Server 因为是使用继承子类实现的，无法通过调用 Swoole\Server 实例的 listen 来方法创建 HTTP 或者 WebSocket 服务器。
// 如果服务器的主要功能为 RPC，但希望提供一个简单的 Web 管理界面。
// 在这样的场景中，可以先创建 HTTP/WebSocket 服务器，然后再进行 listen 监听原生 TCP 的端口。

// 因为 Swoole\Http\Server 和 Swoole\WebSocket\Server 都是集成 Swoole\Server 类，其实例化时，本身就调用了 listen() 方法？【是这个意思吧】所以无法在listen创建。
// =_= 为什么会这样，可能还得去看源码。而要使用 addListener 方法
$http_server = new Swoole\Http\Server('0.0.0.0',9998); 
$http_server->set(array('daemonize'=> false));
$http_server->on('request','request');
//......设置各个回调......
//多监听一个TCP端口，对外开启TCP服务，并设置TCP服务器的回调
$tcp_server = $http_server->addListener('0.0.0.0', 9999, SWOOLE_SOCK_TCP);
//默认新监听的端口 9999 会继承主服务器的设置，也是 HTTP 协议
//需要调用 set 方法覆盖主服务器的设置
$tcp_server->set(array());
$tcp_server->on("receive", function ($serv, $fd, $threadId, $data) {
    echo $data;
});

//********************************************************************* 
// TCP、HTTP、WebSocket 多协议端口复合设置
//********************************************************************* 
$port1 = $server->listen("127.0.0.1", 9501, SWOOLE_SOCK_TCP);
$port1->set([
    'open_websocket_protocol' => true, // 设置使得这个端口支持WebSocket协议
    'open_http_protocol' => false, // 设置这个端口关闭HTTP协议功能
]);
// 同理还有： open_http_protocol、open_http2_protocol、open_mqtt_protocol 等参数
// *可选参数*
// - 监听端口 port 未调用 set 方法，设置协议处理选项的监听端口，将会继承主服务器的相关配置
// - 主服务器为 HTTP/WebSocket 服务器，如果未设置协议参数，监听的端口仍然会设置为 HTTP 或 WebSocket 协议，并且不会执行为端口设置的 onReceive 回调
// - 主服务器为 HTTP/WebSocket 服务器，监听端口调用 set 设置配置参数，会清除主服务器的协议设定。监听端口将变为 TCP 协议。
//   监听的端口如果希望仍然使用 HTTP/WebSocket 协议，需要在配置中增加 open_http_protocol => true 和 open_websocket_protocol => true
// *port 可以通过 set 设置的参数有：*
// - socket 参数：如 backlog、TCP_KEEPALIVE、open_tcp_nodelay、tcp_defer_accept 等
// - 协议相关：如 open_length_check、open_eof_check、package_length_type 等
// - SSL 证书相关：如 ssl_cert_file、ssl_key_file 等
// *可选回调*
// port 未调用 on 方法，设置回调函数的监听端口，默认使用主服务器的回调函数，port 可以通过 on 方法设置的回调有：
// 不同监听端口的回调函数，仍然是相同的 Worker 进程空间内执行
// - TCP 服务器 onConnect onClose onReceive
// - UDP 服务器 onPacket onReceive
// - HTTP 服务器 onRequest
// - WebSocket 服务器 onMessage onOpen onHandshake
