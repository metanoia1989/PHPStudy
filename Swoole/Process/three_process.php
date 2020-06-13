<?php
// 创建 3 个子进程，主进程用 wait 回收进程
// 主进程异常退出时，子进程会继续执行，完成所有任务后退出
use Swoole\Process;

for ($n = 1; $n <= 3; $n++) {
    $process = new Process(function () use ($n) {
        echo 'Child #'.getmypid()." start and sleep {$n}s".PHP_EOL;
        sleep($n);
        echo 'Child #'.getmypid()." exit".PHP_EOL;
    });
    $process->start();
}
for ($n = 3; $n--; ) {
    $status = Process::wait(true);
    echo "Recycled #{$status['id']}, code={$status['code']}, signal={$status['signal']}".PHP_EOL;
}
echo "Parent #".getmypid()." exit".PHP_EOL;