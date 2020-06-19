<?php

namespace App\Jobs\Timer;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

/**
 * 基于 Swoole 定时器实现毫秒级任务调度
 * Linux 自带的 Cron Job（最小粒度是分钟）
 */
class TestCronJob extends CronJob
{
    protected $i = 0;

    /**
     * 类比为 Swooole 定时器中的回调方法
     *
     * @return void
     */
    public function run()
    {
        Log::info(__METHOD__, ['start', $this->i, microtime(true)]);
        $this->i++;
        Log::info(__METHOD__, ['end', $this->i, microtime(true)]);
        if ($this->i == 3) { // 总共运行3次
            Log::info(__METHOD__, ['stop', $this->i, microtime(true)]);
            $this->stop(); // 清除定时器
        }
    }

    /**
     * 每隔 1000ms 执行一次任务
     *
     * @return void
     */
    public function interval()
    {
        return 1000; // 定时器间隔，单位为 ms
    }

    /**
     * 是否在设置之后立即触发run方法执行
     *
     * @return boolean
     */
    public function isImmediate()
    {
        return false;
    }
}
