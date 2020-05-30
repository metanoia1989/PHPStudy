<?php
// WebSocket 服务器
// 完全协程化的 WebSocket 服务器实现，继承自 Co\Http\Server，底层提供了对 WebSocket 协议的支持，在此不再赘述，只说差异。
// 此章节在 v4.4.13 后可用。

//***********************************************************
// Swoole\Coroutine\WebSocket 的处理流程
//***********************************************************
// 处理流程
// $ws->upgrade()：向客户端发送 WebSocket 握手消息
// while(true) 循环处理消息的接收和发送
// $ws->recv() 接收 WebSocket 消息帧
// $ws->push() 向对端发送数据帧
// $ws->close() 关闭连接
// $ws 是一个 Swoole\Http\Response 对象，具体每个方法使用方法参考下文。


//***********************************************************
// Swoole\Http\Response 的方法
//***********************************************************
// upgrade()
// 发送 WebSocket 握手成功信息。
// 此方法不要用于异步风格的服务器中
// Swoole\Http\Response->upgrade(): bool;

// recv()
// 接收 WebSocket 消息。
// 此方法不要用于异步风格的服务器中，调用 recv 方法时会挂起当前协程，等待数据到来时再恢复协程的执行
// Swoole\Http\Response->recv(double timeout = -1): Swoole\WebSocket\Frame | fasle | string;
// 返回值
// - 成功收到消息，返回 Swoole\WebSocket\Frame 对象，请参考 Swoole\WebSocket\Frame
// - 失败返回 false，请使用 swoole_last_error() 获取错误码
// - 连接关闭返回空字符串

// push()
// 发送 WebSocket 数据帧。
// 此方法不要用于异步风格的服务器中，发送大数据包时，需要监听可写，因此会引起多次协程切换
// Swoole\Http\Response->push(string|object $data, int $opcode = 1, bool $finish = true): bool;

// close()
// 关闭 WebSocket 连接。
// 此方法不要用于异步风格的服务器中，在 v4.4.15 以前版本会误报 Warning 忽略即可。
// Swoole\Http\Response->close(): bool;

use function Co\swoole_last_error;

Co\run(function () {
    $server = new Co\Http\Server('0.0.0.0', 9502, false);
    $server->handle('/websocket', function ($request, $ws) {
        $ws->upgrade();
        while (true) {
            $frame = $ws->recv();
            if ($frame === false) {
                echo "error: " . swoole_last_error()."\n";
                break;
            } else if ($frame == '') {
                break;
            } else {
                if ($frame->data == "close") {
                    return $ws->close();
                }
                $ws->push("Hello {$frame->data}!");
                $ws->push("How are you, {$frame->data}?");
            }
        }
    });

    $server->handle('/', function ($request, $response) {
        $html = <<<HTML
    <h1>Swoole WebSocket Server</h1>
    <script>
        var ws_server = 'ws://192.168.0.103:9502/websocket';
        var websocket = new WebSocket(ws_server);
        websocket.onopen = function (evt) {
            console.log("Connected to WebSocket server.");
            websocket.send('hello');
        };
        websocket.onclose = function (evt) {
            console.log('Disconnected');
        };
        websocket.onmessage = function (evt) {
            console.log('Retrieved data from server: ' + evt.data);
        };
        websocket.onerror = function (evt, e) {
            console.log('Error occured: ' + evt.data);
        }
    </script>
HTML;
        $response->end($html);
    });

    $server->start();
});