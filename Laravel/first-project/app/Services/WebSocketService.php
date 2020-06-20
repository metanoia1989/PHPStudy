<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;

/**
 * WebSocket服务
 */
class WebSocketService implements WebSocketHandlerInterface
{
    public function __construct()
    {

    }

    /**
     * 连接建立时触发
     *
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\Http\Request $request
     * @return void
     */
    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request)
    {
        // 在触发WebSocket连接建立事件前， Laravel 应用初始化的生命周期已经结束，在这里可以获取Laravel 请求和会话数据
        // 调用 push 方法向客户端推送数据，fd 是客户端连接标识字段
        Log::info('WebSocket 连接建立: '.$request->fd);
        // 通过 swoole 实例上的 wsTable 属性访问 SwooleTable
        app('swoole')->wsTable->set('fd: '.$request->fd, ['value' => $request->fd]);
        $server->push($request->fd, 'Welcome to WebSocket Server built on LaravelS');
    }

    /**
     * 收到消息时触发
     *
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\WebSocket\Frame $frame
     * @return void
     */
    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame)
    {
        foreach (app('swoole')->wsTable as $key => $row) {
            if (strpos($key, 'fd: ') === 0 && $server->exist($row['value'])) {
                Log::info('Receive message from client: '.$row['value']);
                // 调用 push 向客户端推送数据
                $server->push($frame->fd, 'This is a message sent from WebSocket Server at '.date('Y-m-d H:i:s'));
            }
        }
    }

    /**
     * 关闭连接时触发
     *
     * @param \Swoole\WebSocket\Server $server
     * @param int $fd
     * @param int $reactorId
     * @return void
     */
    public function onClose(\Swoole\WebSocket\Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket连接关闭');
    }
}
