<?php
namespace App\Events;

use App\Listeners\TestEventListener;
use Hhxsv5\LaravelS\Swoole\Task\Event;

/**
 * 自定义的异步事件
 */
class TestEvent extends Event
{
    protected $listeners = [
        // 监听器列表
        TestEventListener::class,
    ];

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
