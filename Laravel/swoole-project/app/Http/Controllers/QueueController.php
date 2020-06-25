<?php

namespace App\Http\Controllers;

use App\Jobs\QueuedTest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;


class QueueController extends Controller
{
    use DispatchesJobs;

    public function index()
    {
        $this->dispatch(new QueuedTest());
        return "发送消息成功";
    }

    public function db()
    {
        $id = $this->dispatch(new QueuedTest());
        // 消息的获取与处理
        $queue = app('Illuminate\Contracts\Queue\Queue');
        $queueJob = $queue->pop();
        $queueJob->fire();

        return "发送消息成功";
    }
}
