<?php
// 消息队列实现进程间通信 =_= 不过好像跑不成功 不知道为啥

use Swoole\Process;

$process = new Process(function (Process $worker) {
    // 子进程逻辑
    // 从消息队列读取数据
    $cmd = $worker->pop();
    echo "Message from master process: " . $cmd . "\n";
    ob_start();
    // 执行外部程序并显示未经处理的原始输出，会直接打印输出
    passthru($cmd);
    $ret = ob_get_clean() ?: "";
    $ret = trim($ret) . ". worker pid: " . $worker->pid. "\n";
    // 将数据推动到消息队列
    $worker->push($ret);
    $worker->exit(0); // 退出子进程
}, false, false); // 关闭管道

// 调用useQueue表示使用消息队列进行进程间通信
// 消息队列与管道通信不能共存
// 第一个参数表示消息队列里的key，第二参数表示通信模式 2位争抢模式
// 使用争抢模式进行通信时，哪个子进程先读取到消息先消费，因此无法实现与指定子进程的通信。
// 消息队列不支持事件循环，因此引入了 \Swoole\Process::IPC_NOWAIT 表示以非阻塞模式进行通信
$process->useQueue(1, 2 | \Swoole\Process::IPC_NOWAIT);
// 从主进程将命令推送到消息列队
$process->push('php --version');
// 从消息队列读取返回消息
$msg = $process->pop();
echo "Message from worker process: ".$msg;

// 启动进程
$process->start();
$process->wait(); // 等待子进程结束

