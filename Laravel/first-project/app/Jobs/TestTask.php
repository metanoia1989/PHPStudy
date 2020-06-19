<?php
namespace App\Jobs;

use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 基于 Swoole 实现异步任务队列
 */
class TestTask extends Task
{
    // 待处理任务数据
    private $data;

    // 任务处理结果
    private $result;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 任务投递调用 task 回调时重复啊，等同于 Swoole 中的 onTask 逻辑
     *
     * @return void
     */
    public function handle()
    {
        Log::info(__CLASS__.': 开始处理任务', [$this->data]);
        // todo 耗时任务具体处理逻辑在这里编写
        sleep(3); // 模拟任务需要3秒才能执行完成
        $this->result = 'The result of '.$this->data.' is balabalabala';
    }

    /**
     * 任务完成后调用 finish 回调时触发，等同于 onFinish 逻辑
     *
     * @return void
     */
    public function finish()
    {
        Log::info(__CLASS__.': 任务处理完成', [$this->result]);
        // 可以在这里写触发后续要执行的任务，或者执行其他善后逻辑
    }
}
