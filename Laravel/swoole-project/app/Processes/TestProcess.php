<?php

namespace App\Processes;

use App\Jobs\TestTask;
use Hhxsv5\LaravelS\Swoole\Process\CustomProcessInterface;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;
use Swoole\Coroutine;

/**
 * 自定义进程
 */
class TestProcess implements CustomProcessInterface
{
    public static function getName()
    {
        return "test"; // 进程名称
    }

    public static function callback(\Swoole\Http\Server $swoole, \Swoole\Process $process)
    {
            // 回调函数不能退出，一旦退出，Manager会自动创建该进程，频繁退出/创建进程会消耗系统性能
            Log::info(__METHOD__, [posix_getpid(), $swoole->stats()]);
            while (true) {
                Log::info("随便做点什么...");
                // 相当于PHP的sleep函数，但是底层会自动启动协程，让出时间片，睡眠时间结束后恢复运行
                Coroutine::sleep(20);
                // 在自定义进程中调度任务，但不支持的finish回调
                // 注意：
                // 1、第二个参数设置为true，表示通过消息管道通信
                // 2. 在 config/laravels.php 配置文件中设置 task_ipc_mode 为 1 或 2
                // task_ipc_mode 用于设置 task_worker 与 worker 进程之间的通信模式，1 表示通过 unix socket，2 表示使用消息队列
                $ret = Task::deliver(new TestTask('task data'), true);
                var_dump($ret);
                // 上一层会捕获本函数抛出的异常并将其记录到Swoole日志中，如果异常数超过10个，该进程会退出，然后Master进程会重新创建这个进程
            }
    }

    public static function onReload(\Swoole\Http\Server $swoole, \Swoole\Process $process)
    {
        // 进程结束
        $process->exit(0);
    }
}
