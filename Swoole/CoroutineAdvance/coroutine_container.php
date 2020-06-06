<?php
//****************************************************** 
// Coroutine\Scheduler 协程容器
//****************************************************** 
// 所有的协程必须在协程容器里面创建，Swoole 程序启动的时候大部分情况会自动创建协程容器，用 Swoole 启动程序的方式一共有三种：
// 所有的协程必须在协程容器里面创建，Swoole 程序启动的时候大部分情况会自动创建协程容器，用 Swoole 启动程序的方式一共有三种：
// - 调用异步风格服务端程序的 start 方法，此种启动方式会在事件回调中创建协程容器，参考 enable_coroutine。
// - 调用 Swoole 提供的 2 个进程管理模块 Process 和 Process\Pool 的 start 方法，此种启动方式会在进程启动的时候创建协程容器，参考这两个模块构造函数的 enable_coroutine 参数。
// - 其他直接裸写协程的方式启动程序，需要先创建一个协程容器 (Co\run() 函数，可以理解为 java、c 的 main 函数)，例如：
// 在 Swoole v4.4+ 版本可用。
// Co\run() 函数其实是对 Swoole\Coroutine\Scheduler 类 (协程调度器类) 的封装，想了解细节的同学可以看 Swoole\Coroutine\Scheduler 的方法
// 不可以嵌套 Co\run()。 Co\run() 里面的逻辑如果有未处理的事件在 Co\run() 之后就进行 EventLoop，后面的代码将得不到执行，反之，如果没有事件了将继续向下执行，可以再次 Co\run()。
// *启动一个全协程 HTTP 服务*
Co\run(function () {
    $server = new Co\Http\Server("127.0.0.1", 9502, false);
    $server->handle('/', function ($request, $response) {
        $response->end("<h1>Index</h1>");
    });
    $server->handle('/test', function ($request, $response) {
        $response->end("<h1>Test</h1>");
    });
    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end("<h1>Stop</h1>");
        $server->shutdown();
    });
    $server->start();
});
echo 1;//得不到执行

// *添加 2 个协程并发的做一些事情*
Co\run(function () {
    go(function() {
        var_dump(file_get_contents("http://www.xinhuanet.com/"));
    });

    go(function() {
        Co::sleep(1);
        echo "done\n";
    });
});
echo 1;//可以得到执行


//****************************************************** 
// Coroutine\Scheduler 方法
//****************************************************** 
// set()
// 设置协程运行时参数。
// 是 Coroutine::set 方法的别名。请参考 Coroutine::set 文档
// Swoole\Coroutine\Scheduler->set(array $options): bool;
$sch = new Co\Scheduler;
$sch->set(['max_coroutine' => 100]);

// add()
// 添加任务。
// Swoole\Coroutine\Scheduler->add(callable $fn, ... $args): bool;
// 与 go 函数不同，这里添加的协程不会立即执行，而是等待调用 start 方法时，一起启动并执行。如果程序中仅添加了协程，未调用 start 启动，协程函数 $fn 将不会被执行。
$scheduler = new Swoole\Coroutine\Scheduler;
$scheduler->add(function ($a, $b) {
    Co::sleep(1);
    echo assert($a == 'hello') . PHP_EOL;
    echo assert($b == 12345) . PHP_EOL;
    echo "Done.\n";
}, "hello", 12345);
$scheduler->start();

// parallel()
// 添加并行任务。
// 与 add 方法不同，parallel 方法会创建并行协程。在 start 时会同时启动 $num 个 $fn 协程，并行地执行。
// Swoole\Coroutine\Scheduler->parallel(int $num, callable $fn, ... $args): bool;
$sch = new Swoole\Coroutine\Scheduler;
$sch->parallel(10, function ($t, $n) {
    Co::sleep($t);
    echo "Co ".Co::getCid()."\n";
}, 0.05, 'A');
$sch->start();

// start()
// 启动程序。
// 遍历 add 和 parallel 方法添加的协程任务，并执行。
// Swoole\Coroutine\Scheduler->start(): bool;
// 返回值
// - 启动成功，会执行所有添加的任务，所有协程退出时 start 会返回 true
// - 启动失败返回 false，原因可能是已经启动了或者已经创建了其他调度器无法再次创建