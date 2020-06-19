<?php
namespace App\Events;

use Hhxsv5\LaravelS\Swoole\Task\Event;

/**
 * 自定义的异步事件
 */
class TestEvent extends Event
{
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
