<?php

use App\Count;
use Swoole\Http\Request;
use App\Services\WebSocket\WebSocket;
use App\Services\Websocket\Facades\WebSocket as WebSocketProxy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * WebSocket Routes
 *
 * Here is where you can register websocket events for you application
 */


WebSocketProxy::on('connect', function (WebSocket $websocket, Request $request) {
    // 发送欢迎消息
    $websocket->setSender($request->fd);
    $websocket->emit('connect', '欢迎访问聊天室');
});

WebSocketProxy::on('disconnect', function (WebSocket $websocket, $data) {
    // called while socket on disconnect
    roomout($websocket, $data);
    $websocket->logout();
});

WebSocketProxy::on('login', function (WebSocket $websocket, $data) {
    if (!empty($data['token']) && ($user = \App\User::where('api_token', $data['token'])->first())) {
        $websocket->loginUsing($user);
        $websocket->toUser($user)->emit('login', '登录成功');
        // TODO 读取未读消息
        $rooms = [];
        foreach (\App\Count::$ROOMLIST as $roomid) {
            $result = \App\Count::where('user_id', $user->id)->where('room_id', $roomid)->first();
            $roomid = 'room'.$roomid;
            if ($result) {
                $rooms[$roomid] = $result->count;
            } else {
                $rooms[$roomid] = 0;
            }
        }
        $websocket->toUser($user)->emit('count', $rooms);
    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
});

/**
 * 进入房间
 * 要确保用户已经登录（有人提议说用户认证实现可以通过中间件来处理，是的，后面我会统一优化下），
 * 然后要确保客户端传递过来了房间号，否则无法与指定聊天室房间关联。
 */
WebSocketProxy::on('room', function (WebSocket $websocket, $data) {
    if ($userId = $websocket->getUserId()) {
        $user = User::find($userId);
        // 从请求数据中获取房间ID
        if (empty($data['roomid'])) {
            return;
        }
        $roomId = $data['roomid'];
        // 重置用户与fd关联
        Redis::hset('socket_id', $user->id, $websocket->getSender());
        // 将该房间下用户未读消息清零
        $count = Count::where('user_id', $user->id)->where('room_id', $roomId)->first();
        $count->count = 0;
        $count->save();
        // 将用户加入指定房间
        $room = Count::$ROOMLIST[$roomId];
        $websocket->join($room);
        // 打印日志
        Log::info($user->name."进入房间: ".$room);
        // 更新在线用户信息
        $roomUsersKey = 'online_users_'.$room;
        $onlineUsers = Cache::get($roomUsersKey);
        $user->src = $user->avatar;
        if ($onlineUsers) {
            $onlineUsers[$user->id] = $user;
            Cache::forever($roomUsersKey, $onlineUsers);
        } else {
            $onlineUsers = [
                $user->id => $user
            ];
            Cache::forever($roomUsersKey, $onlineUsers);
        }
        // 广播消息给房间内所有用户
        $websocket->to($room)->emit('room', $onlineUsers);
    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
});

WebSocketProxy::on('roomout', function (WebSocket $websocket, $data) {
    roomout($websocket, $data);
});

function roomout(WebSocket $websocket, $data)
{
    if ($userId = $websocket->getUserId()) {
        $user = User::find($userId);
        if (empty($data['roomid'])) {
            return;
        }
        $roomId = $data['roomid'];
        $room = Count::$ROOMLIST[$roomId];
        // 更新在线用户信息
        $roomUsersKey = 'online_users_'.$room;
        $onlineUsers = Cache::Get($roomUsersKey);
        if (!empty($onlineUsers[$user->id])) {
            unset($onlineUsers[$user->id]);
            Cache::forever($roomUsersKey, $onlineUsers);
        }
        $websocket->to($room)->emit('roomout', $onlineUsers);
        Log::info($user->name.'退出房间：'.$room);
        $websocket->leave($room);
    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
}
