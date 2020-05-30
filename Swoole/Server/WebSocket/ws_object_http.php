<?php
// 通过接收 http 触发所有 websocket 的推送，面向对象可以把 WebSocket\Server 设置成一个成员属性
class WebSocketTest
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9501);
        $this->server->on('open', function (Swoole\WebSocket\Server $server, $request) {
            echo "server: handshake success with fd {$request->fd}\n";
        });

        $this->server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
            echo "receive from {$frame->fd}: {$frame->date}, opcode: {$frame->opcode}, fin: {$frame->finish}\n";
            $server->push($frame->fd, "this is server");
        });

        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $this->server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
            // 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                // 先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, $request->get['message']);
                }
            }
            $response->end("ouput end");
        });


        $this->server->start();
    }
}

new WebSocketTest();