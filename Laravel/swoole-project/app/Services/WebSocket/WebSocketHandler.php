<?php
namespace App\Services\WebSocket;

use App\Events\MessageReceived;
use App\Services\WebSocket\Pusher;
use App\Services\WebSocket\SocketIO\Packet;
use App\Services\WebSocket\SocketIO\SocketIOParser;
use App\Services\WebSocket\WebSocket;
use App\User;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;

/**
 * 很简单，就是在建立、断开 WebSocket 连接的时候打印下日志，然后在收到客户端发送过来的弹幕消息时
 * 将其推送给所有已连接的 WebSocket 客户端，达到「广播」的效果，这样，就不需要客户端主动来拉数据了。
 * 当然，这里是最简单的推送逻辑，你可以根据需要将弹幕消息保存到数据库或其他存储设备持久化存储。
 */
class WebSocketHandler implements WebSocketHandlerInterface
{
    /**
     * @var WebSocket
     */
    protected $websocket;

    /**
     * @var Parser
     */
    protected $parser;


    public function __construct()
    {
        Log::info('消息解析类 swoole.parser', [app('swoole.parser')]);
        $this->websocket = app('swoole.websocket');
        $this->parser = app('swoole.parser');
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
        if (!request()->input('sid')) {
            // 初始化连接信息适配 socket.io-client，这段代码不能省略，否则无法建立连接
            $payload = json_encode([
                'sid' => base64_encode(uniqid()),
                'upgrades' => [],
                'pingInterval' => config('laravels.swoole.heartbeat_idle_time') * 1000,
                'pingTimeout' => config('laravels.swoole.heartbeat_check_interval') * 1000,
            ]);
            $initPayload = Packet::OPEN . $payload;
            $connectPayload = Packet::MESSAGE . Packet::CONNECT;
            $server->push($request->fd, $initPayload);
            $server->push($request->fd, $connectPayload);
            return;
        }
        Log::info('WebSocket连接建立：'.$request->fd);
        if ($this->websocket->eventExists('connect')) {
            $this->websocket->call('connect', $request);
        }
    }

    /**
     * 收到消息时触发
     * 在收到消息的回调方法 onMessage 中，首先调用 Parser 类的 execute 方法判断是否是心跳连接，
     * 如果是心跳连接的话跳过不做处理， 否则的话将收到的信息进行解码，经过简单处理后，
     * 再经由 Pusher 类的 push 方法发送回给客户端。
     *
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\WebSocket\Frame $frame
     * @return void
     */
    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame)
    {
        // $frame->fd 是客户端 id，$frame->data 是客户端发送的数据
        Log::info("从 {$frame->fd} 接收到的数据：{$frame->data}");
        if ($this->parser->execute($server, $frame)) {
            // 跳过心跳连接处理
            return;
        }
        $payload = $this->parser->decode($frame);
        ['event' => $event, 'data' => $data ] = $payload;
        $payload = $this->parser->decode($frame);
        $this->websocket->reset(true)->setSender($frame->fd);
        if ($this->websocket->eventExists('connect')) {
            $this->websocket->call($event, $data);
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
        $this->websocket->setSender($fd);
        if ($this->websocket->eventExists('disconnect')) {
            $this->websocket->call('disconnect', '连接关闭');
        }
    }
}
