<?php
// 管道通信 异步非阻塞

use Swoole\Process;

$process = new Process(function (Process $worker) {
    // 子进程逻辑
    swoole_event_add($worker->pipe, function ($pipe) use ($worker) {
        // 通过管道从主进程读取数据
        $cmd = $worker->read();
        ob_start();
        // 执行外部程序并显示未经处理的原始输出，会直接打印输出
        passthru($cmd);
        $ret = ob_get_clean() ?: "";
        $ret = trim($ret) . ". worker pid: " . $worker->pid. "\n";
        // 将数据写入管道
        $worker->write($ret);
        $worker->exit(0); // 退出子进程
    });
}); // Process 构造哈数第三个参数默认为true，启用管道，如果第二个参数也设置为true，则在子进程中可以通过echo将数据写入管道

// 启动进程
$process->start();
// 从主进程将通过管道发送数据到子进程
$process->write('php --version');
// 从子进程读取返回数据并打印
$msg = $process->read();
echo "result from worker: ".$msg;
