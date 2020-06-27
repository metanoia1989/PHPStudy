<?php

namespace App\Http\Controllers;

use App\Count;
use App\Friend;
use App\Http\Resources\MessageResource;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function history(Request $request)
    {
        $roomId = intval($request->get('roomid'));
        $msgid = intval($request->get('msgid')) ;
        if ($roomId <= 0 ) {
            Log::error('无效的房间信息');
            return response()->json([
                'data' => [
                    'errno' => 1,
                    'data' => '无效的房间信息' .$roomId,
                ],
            ]);
        }
        $limit = 20; // 每页显示20条信息
        if ($msgid) {
            $messages = Message::with('user')
                ->where('room_id', $roomId)
                ->where('id', '<', $msgid)
                ->orderBy('id', 'asc')
                ->take($limit)
                ->get();
        } else {
            $messages = Message::with('user')
                ->where('room_id', $roomId)
                ->orderBy('id', 'asc')
                ->take($limit)
                ->get();
        }


        $messagesData = [];
        if ($messages) {
            $messagesData = MessageResource::collection($messages);
        }
        // 返回响应消息
        return response()->json([
            'errno' => 0,
            'data' => [
                "data" => $messagesData,
            ]
        ]);
    }

    /**
     * 获取用户关联的房间的所有消息
     *
     * @return array
     */
    public function byUser()
    {
        $user = Auth::guard('api')->user();
        $friends = Friend::where('user_id', $user->id)->get();
        $roomIds = $friends ? $friends->pluck('friend_id')->concat(collect(Count::$ROOMLIST)) : Count::$ROOMLIST ;
        $allMsg = Message::with('user')
            ->whereIn('room_id', $roomIds)
            ->orderBy('room_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        $messagesData = [];
        if ($allMsg) {
            $data = MessageResource::collection($allMsg);
            foreach ($data as $key => $value) {
                $messagesData[$value['room_id']][] = $value;
            }
        }
        // 返回响应消息
        return response()->json([
            'data' => [
                'errno' => 0,
                'data' => $messagesData,
            ],
        ]);
    }
}
