<?php
//*********************************************** 
// 协程 API 方法
//*********************************************** 

// set()
// 协程设置，设置协程相关选项。
// Swoole\Coroutine::set(array $options);
// 参数	此版本后稳定	作用
// max_coroutine	-	设置全局最大协程数，超过限制后底层将无法创建新的协程，Server 下会被 server->max_coroutine 覆盖。
// stack_size	-	设置单个协程初始栈的内存尺寸，默认为 2M
// log_level	v4.0.0	日志等级 详见
// trace_flags	v4.0.0	跟踪标签 详见
// socket_connect_timeout	v4.2.10	建立连接超时时间，参考客户端超时规则
// socket_read_timeout	v4.3.0	读超时，参考客户端超时规则
// socket_write_timeout	v4.3.0	写超时，参考客户端超时规则
// socket_dns_timeout	v4.4.0	域名解析超时，参考客户端超时规则
// socket_timeout	v4.2.10	发送 / 接收超时，参考客户端超时规则
// dns_cache_expire	v4.2.11	设置 swoole dns 缓存失效时间，单位秒，默认 60 秒
// dns_cache_capacity	v4.2.11	设置 swoole dns 缓存容量，默认 1000
// hook_flags	v4.4.0	一键协程化的 hook 范围配置，参考一键协程化
// enable_preemptive_scheduler	v4.4.0	设置打开写成抢占式调度，协程最大执行时间为 10ms，会覆盖 ini 配置
// dns_server	v4.5.0	设置 dns 查询的 server，默认 "8.8.8.8"
// exit_condition	v4.5.0	传入一个 callable，返回 bool，可自定义 reactor 退出的条件。如：我希望协程数量等于 0 时程序才退出，
// 则可写 Co::set(['exit_condition' => function() { return Coroutine::stats()['coroutine_num'] === 0; }])

// create()
// 创建一个新的协程，并立即执行。
// Swoole\Coroutine::create(callable $function, ...$args) : int|false;
// go(callable $function, ...$args) : int|false; // 参考php.ini的use_shortname配置
// *返回值*
// - 创建失败返回 false
// - 创建成功返回协程的 ID
// 由于底层会优先执行子协程的代码，因此只有子协程挂起时，Coroutine::create 才会返回，继续执行当前协程的代码。
// *执行顺序*
// 在一个协程中使用 go 嵌套创建新的协程。因为 Swoole 的协程是单进程单线程模型，因此：
// - 使用 go 创建的子协程会优先执行，子协程执行完毕或挂起时，将重新回到父协程向下执行代码
// - 如果子协程挂起后，父协程退出，不影响子协程的执行
go(function() {
  go(function () {
      co::sleep(3.0);
      go(function () {
          co::sleep(2.0);
          echo "co[3] end\n";
      });
      echo "co[2] end\n";
  });

  co::sleep(1.0);
  echo "co[1] end\n";
});
// 协程开销
// 每个协程都是相互独立的，需要创建单独的内存空间 (栈内存)，在 PHP-7.2 版本中底层会分配 8K 的 stack 来存储协程的变量，zval 的尺寸为 16字节，
// 因此 8K 的 stack 最大可以保存 512 个变量。协程栈内存占用超过 8K 后 ZendVM 会自动扩容。
// 协程退出时会释放申请的 stack 内存。
// - PHP-7.1、PHP-7.0 默认会分配 256K 栈内存
// - 可调用 Co::set(['stack_size' => 4096]) 修改默认的栈内存尺寸

// defer()
// defer 用于资源的释放，会在协程关闭之前 (即协程函数执行完毕时) 进行调用，就算抛出了异常，已注册的 defer 也会被执行。
// Swoole 版本 >= 4.2.9
// Swoole\Coroutine::defer(callable $function);
// defer(callable $function); // 短名API
// 需要注意的是，它的调用顺序是逆序的（先进后出）, 也就是先注册 defer 的后执行，先进后出。
// 逆序符合资源释放的正确逻辑，后申请的资源可能是基于先申请的资源的，如先释放先申请的资源，后申请的资源可能就难以释放。
go(function () {
    global $db;
    defer(function () use ($db) {
        $db->close();
    });
});

// exists()
// 判断指定协程是否存在。
// Swoole\Coroutine::exists(int $cid = 0): bool
// Swoole 版本 >= v4.3.0
go(function () {
  go(function () {
      go(function () {
          Co::sleep(0.001);
          var_dump(Co::exists(Co::getPcid())); // 1: true
      });
      go(function () {
          Co::sleep(0.003);
          var_dump(Co::exists(Co::getPcid())); // 3: false
      });
      Co::sleep(0.002);
      var_dump(Co::exists(Co::getPcid())); // 2: false
  });
});

// getCid()
// 获取当前协程的唯一 ID, 它的别名为 getUid, 是一个进程内唯一的正整数。
// Swoole\Coroutine::getCid(): int
// 返回值
// - 成功时返回当前协程 ID
// - 如果当前不在协程环境中，则返回 -1

// getPcid()
// 获取当前协程的父 ID。
// Swoole\Coroutine::getPcid([$cid]): int
// - 非嵌套协程调用 getPcid 将返回 -1 (从非协程空间创建的)
// - 在非协程内调用 getPcid 将返回 false (没有父协程)
// - 0 作为保留 id, 不会出现在返回值中
// 协程之间并没有实质上的持续父子关系，协程之间是相互隔离，独立运作的，此 Pcid 可理解为创建了当前协程的协程 id
// *用处：串联多个协程调用栈*
go(function () {
    go(function () {
        $ptrace = Co::getBackTrace(Co::getPcid());
        // balababala
        var_dump(array_merge($ptrace, Co::getBackTrace(Co::getCid())));
    });
});

// getContext()
// 获取当前协程的上下文对象。
// Swoole\Coroutine::getContext([$cid]): Swoole\Coroutine\Context
// Swoole 版本 >= v4.3.0
// *作用*
// - 协程退出后上下文自动清理 (如无其它协程或全局变量引用)
// - 无 defer 注册和调用的开销 (无需注册清理方法，无需调用函数清理)
// - 无 PHP 数组实现的上下文的哈希计算开销 (在协程数量巨大时有一定好处)
// - Co\Context 使用 ArrayObject, 满足各种存储需求 (既是对象，也可以以数组方式操作)

// yield()
// 手动让出当前协程的执行权。而不是基于 IO 的协程调度
// 此方法拥有另外一个别名：Coroutine::suspend()
// 必须与 Coroutine::resume() 方法成对使用。该协程 yield 以后，必须由其他外部协程 resume，否则将会造成协程泄漏，被挂起的协程永远不会执行。
// Swoole\Coroutine::yield();
$cid = go(function () {
    echo "co 1 start\n";
    co::yield();
    echo "co 1 end\n";
});

go(function () use ($cid) {
    echo "co 2 start\n";
    co::sleep(0.5);
    co::resume($cid);
    echo "co 2 end\n";
});

// resume()
// 手动恢复某个协程，使其继续运行，不是基于 IO 的协程调度。
// 当前协程处于挂起状态时，另外的协程中可以使用 resume 再次唤醒当前协程
// Swoole\Coroutine::resume(int $coroutineId);
use Swoole\Coroutine as co;
$id = go(function(){
    $id = co::getUid();
    echo "start coro $id\n";
    co::suspend();
    echo "resume coro $id @1\n";
    co::suspend();
    echo "resume coro $id @2\n";
});
echo "start to resume $id @1\n";
co::resume($id);
echo "start to resume $id @2\n";
co::resume($id);
echo "main\n";

// list()
// 遍历当前进程内的所有协程。
// Swoole\Coroutine::list(): Coroutine\Iterator
// Swoole\Coroutine::listCoroutines(): Coroitine\Iterator
// v4.3.0 以下版本需使用 listCoroutines, 新版本缩略了该方法的名称并将 listCoroutines 设为别名。list 在 v4.1.0 或更高版本可用。
// 返回值
// - 返回迭代器，可使用 foreach 遍历，或使用 iterator_to_array 转为数组
$coros = Swoole\Coroutine::listCoroutines();
foreach($coros as $cid)
{
    var_dump(Swoole\Coroutine::getBackTrace($cid));
}

// stats()
// 获取协程状态。
// Swoole\Coroutine::stats(): array
// 返回值
// key	作用
// event_num	当前 reactor 事件数量
// signal_listener_num	当前监听信号的数量
// aio_task_num	异步 IO 任务数量 (这里的 aio 指文件 IO 或 dns, 不包含其它网络 IO, 下同)
// aio_worker_num	异步 IO 工作线程数量
// c_stack_size	每个协程的 C 栈大小
// coroutine_num	当前运行的协程数量
// coroutine_peak_num	当前运行的协程数量的峰值
// coroutine_last_cid	最后创建协程的 id
var_dump(Swoole\Coroutine::stats());
// array(1) {
//   ["c_stack_size"]=>
//   int(2097152)
//   ["coroutine_num"]=>
//   int(132)
//   ["coroutine_peak_num"]=>
//   int(2)
// }

// getBackTrace()
// 获取协程函数调用栈。
// Swoole\Coroutine::getBackTrace(int $cid=0, int $options=DEBUG_BACKTRACE_PROVIDE_OBJECT, int $limit=0): array;
// Swoole 版本 >= v4.1.0
// 返回值
// - 指定的协程不存在，将返回 false
// - 成功返回数组，格式与 debug_backtrace 函数返回值相同

// getElapsed()
// 获取协程运行的时间以便于分析统计或找出僵尸协程
// Swoole 版本 >= v4.5.0 可用
// Swoole\Coroutine::getElapsed(): float
// 返回值
// - 协程已运行的时间浮点数，毫秒级精度

// batch()
// 并发执行多个协程，并且通过数组，返回这些协程方法的返回值。
// Swoole 版本 >= v4.5.2 可用
// Swoole\Coroutine\batch(array $tasks, float $timeout = -1): array
// 返回值
// 返回一个数组，里面包含回调的返回值。如果 $tasks 参数中，指定了 key，则返回值也会被该 key 指向。
use Swoole\Coroutine;
use function Swoole\Coroutine\batch;

Coroutine::set(['hook_flags' => SWOOLE_HOOK_ALL]);

$start_time = microtime(true);
Coroutine\run(function () {
    $use = microtime(true);
    $results = batch([
        'file_put_contents' => function () {
            return file_put_contents(__DIR__ . '/greeter.txt', "Hello,Swoole.");
        },
        'gethostbyname' => function () {
            return gethostbyname('localhost');
        },
        'file_get_contents' => function () {
            return file_get_contents(__DIR__ . '/greeter.txt');
        },
        'sleep' => function () {
            sleep(1);
            return true; // 返回NULL 因为超过了设置的超时时间0.1秒，超时后会立即返回。但正在运行的协程会继续执行完毕，而不会中止。
        },
        'usleep' => function () {
            usleep(1000);
            return true;
        },
    ], 0.1);
    $use = microtime(true) - $use;
    echo "Use {$use}s, Result:\n";
    var_dump($results);
});
$end_time =  microtime(true) - $start_time;
echo "Use {$end_time}s, Done\n";