<?php

use App\Count;
use App\Message;
use Swoole\Http\Request;
use App\Services\WebSocket\WebSocket;
use App\Services\Websocket\Facades\WebSocket as WebSocketProxy;
use Carbon\Carbon;
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

/**
 * 消息发送和广播功能
 * 在确保用户已认证、房间号和消息内容不为空的前提下，
 * 获取到客户端发送的文本消息（含 Emoji 表情）后，
 * 将其保存到 messages 表，然后将其广播给房间内的所有用户即可，
 */
WebSocketProxy::on('message', function (WebSocket $websocket, $data) {
    if ($userId = $websocket->getUserId()) {
        $user = User::find($userId);
        // 获取消息内容
        $msg = $data['msg'];
        $img = $data['img'];
        $roomId = intval($data['roomid']);
        $time = $data['time'];
        // 消息内容或房间号不能为空
        if(empty($msg) && empty($img) || empty($roomId)) {
            return;
        }
        // 记录日志
        Log::info($user->name."在房间里{$roomId}中发布消息：$msg");
        // 将消息保存到数据库（图片消息除外，因为在上传过程中已保存）
        if (empty($img)) {
            $message = new Message();
            $message->user_id = $user->id;
            $message->room_id = $roomId;
            $message->msg = $msg; // 文本消息
            $message->img = ''; // 图片消息留空
            $message->created_at = Carbon::now();
            $message->save();
        }
        // 将消息广播给房间内所有用户
        $room = Count::$ROOMLIST[$roomId];
        $messageData = [
            'userid' => $user->name,
            'username' => $user->name,
            'src' => $user->avatar,
            'msg' => $msg,
            'img' => $img,
            'roomid' => $roomId,
            'time' => $time,
        ];
        $websocket->to($room)->emit('message', $messageData);
        // 更新所有用户本房间未读消息数
        $userIds = Redis::hgetall('socket_id');
        foreach ($userIds as $userId => $socketId) {
            // 更新每个用户未读消息数并将其发送给对应在线用户
            $result = Count::where('user_id', $userId)->where('room_id', $roomId)->first();
            if ($result) {
                $result->count += 1;
                $result->save();
                $rooms[$room] = $result->count;
            } else {
                // 如果某个用户未读消息数记录不存在，则初始化它
                $count = new Count();
                $count->user_id = $user->id;
                $count->room_id = $roomId;
                $count->count = 1;
                $count->save();
                $rooms[$room] = 1;
            }
            $websocket->to($socketId)->emit('count', $rooms);
        }
    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
});
