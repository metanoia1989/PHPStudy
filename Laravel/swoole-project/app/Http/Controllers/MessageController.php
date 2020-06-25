<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function history(Reuqest $request)
    {
        $roomId = intval($request->get('roomid'));
        $current = intval($request->get('current'));
        $total = intval($request->get('total'));
        if ($roomId <= 0 || $current <= 0) {
            Log::error('无效的房间和页面信息');
            return response()->json([
                'data' => [
                    'errno' => 1,
                    'data' => '无效的房间和页面信息'
                ],
            ]);
        }
        // 获取消息总数
        $messageTotal = Message::where('room_id', $roomId)->count();
        $limit = 20; // 每页显示20条信息
        $skip = ($current - 1) * 20; // 从第多少条消息开始
        // 分页查询消息
        $messages = Message::where('room_id', $roomId)
            ->skip($skip)
            ->orderBy('created_at', 'asc')
            ->get();
        $messagesData = [];
        if ($messages) {
            $messagesData = MessageResource::collection($messages);
        }
        // 返回响应消息
        return response()->json([
            'data' => [
                'errno' => 0,
                'data' => $messagesData,
                'total' => $messageTotal,
                'current' => $current,
            ],
        ]);
    }
}
