<?php

namespace App\Events;

use App\Listeners\MessageListener;
use App\Message;
use Carbon\Carbon;
use Hhxsv5\LaravelS\Swoole\Task\Event;

/**
 * 接收用户消息，将消息入库
 * 对传入的数据进行格式转化，这里我们从外部传入消息对象和用户ID，
 * 然后通过 getData 方法将其组合为 Message 模型实例并返回。
 */
class MessageReceived extends Event
{

    protected $listeners = [
        MessageListener::class,
    ];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $userId = 0)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * 获取消息数据
     *
     * @return App\Message
     */
    public function getData()
    {
        $model = new Message();
        $model->room_id = $this->message->room_id;
        $model->msg = $this->message->type == 'text' ? $this->message->content : '';
        $model->img = $this->message->type == 'image' ? $this->message->image : '';
        $model->user_id = $this->userId;
        $model->created_at = Carbon::now();
        return $model;
    }

}
