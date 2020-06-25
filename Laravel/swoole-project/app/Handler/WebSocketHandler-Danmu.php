<?php
namespace App\Handler;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;

/**
 * Swoole+Vue实现弹幕功能
 * 很简单，就是在建立、断开 WebSocket 连接的时候打印下日志，然后在收到客户端发送过来的弹幕消息时
 * 将其推送给所有已连接的 WebSocket 客户端，达到「广播」的效果，这样，就不需要客户端主动来拉数据了。
 * 当然，这里是最简单的推送逻辑，你可以根据需要将弹幕消息保存到数据库或其他存储设备持久化存储。
 */
class WebSocketHandler implements WebSocketHandlerInterface
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
        Log::info('WebSocket连接建立：'.$request->fd);
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
        // $frame->fd 是客户端 id，$frame->data 是客户端发送的数据
        Log::info("从 {$frame->fd} 接收到的数据：{$frame->data}");
        foreach ($server->connections as $fd) {
            if (!$server->isEstablished($fd)) {
                // 如果连接不可用则忽略
                continue;
            }
            $server->push($fd, $frame->data); // 向所有连接的客户端发送数据
        }
    }

    /**
     * 连接关闭时触发
     *
     * @param \Swoole\WebSocket\Server $server
     * @param int $fd
     * @param int $reactorId
     * @return void
     */
    public function onClose(\Swoole\WebSocket\Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket连接关闭：'.$fd);
    }
}
