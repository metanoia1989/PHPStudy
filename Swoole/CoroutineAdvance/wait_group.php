<?php
//******************************************************************************* 
// WaitGroup
//******************************************************************************* 
// 在 Swoole4 中可以使用 Channel 实现协程间的通信、依赖管理、协程同步。基于 Channel 可以很容易地实现 Golang 的 sync.WaitGrup 功能。

//******************************************************************************* 
// 实现代码
//******************************************************************************* 
// 此功能是使用 PHP 编写的功能，并不是 c/c++ 代码，实现源代码在 Library 当中
// add 方法增加计数
// done 表示任务已完成
// wait 等待所有任务完成恢复当前协程的执行
// WaitGroup 对象可以复用，add、done、wait 之后可以再次使用

Co\run(function () {
    $wg = new \Swoole\Coroutine\WaitGroup();
    $result = [];

    $wg->add();

    // 启动第一个协程
    go(function () use ($wg, &$result) {
        // 启动一个协程客户端client，请求淘宝首页
        $cli = new \Swoole\Coroutine\Http\Client('www.taobao.com', 443, true);
        $cli->setHeaders([
            'Host' => 'www.taobao.com',
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/index.php');
        $result['taobao'] = $cli->body;
        $cli->close();
        $wg->done();
    });

    $wg->add();

    // 启动第二个协程
    go(function () use ($wg, &$result) {
        // 启动一个协程客户端client，请求淘宝首页
        $cli = new \Swoole\Coroutine\Http\Client('www.baidu.com', 443, true);
        $cli->setHeaders([
            'Host' => 'www.baidu.com',
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/index.php');
        $result['baidu'] = $cli->body;
        $cli->close();
        $wg->done();
    });

    // 挂起当前协程，等待所有任务完成后恢复
    $wg->wait();
    var_dump($result);
});