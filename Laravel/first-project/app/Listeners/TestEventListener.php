<?php
namespace App\Listeners;

use Hhxsv5\LaravelS\Swoole\Task\Event;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Illuminate\Support\Facades\Log;

class TestEventListener extends Listener
{

    public function handle()
    {
        Log::info(__CLASS__.': 开始处理', [$this->event->getData()]);
        sleep(3); // 模拟耗时代码的执行
        Log::info(__CLASS__.': 处理完毕');
    }
}
