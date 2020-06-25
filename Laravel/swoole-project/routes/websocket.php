<?php

use Swoole\Http\Request;
use App\Services\WebSocket\WebSocket;
use App\Services\Websocket\Facades\WebSocket as WebSocketProxy;
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

WebSocketProxy::on('disconnect', function (WebSocket $webSocket) {
    // called while socket on disconnect
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


