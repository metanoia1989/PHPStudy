<?php
// 采用 Hook 原生 PHP 函数的方式实现协程客户端，通过一行代码就可以让原来的同步 IO 的代码变成可以协程调度的异步 IO，即一键协程化。
// 此特性在 v4.3 版本后开始稳定，能 Hook 的函数也越来越多，所以有些之前写的协程客户端已经不再推荐使用了，详情查看协程客户端， 
// 例如：在 v4.3+ 支持了文件操作 (file_get_contents、fread 等) 的 Hook，如果您使用的是 v4.3+ 版本就可以直接使用 Hook 而不是使用 Swoole 提供的协程文件操作了。

//*********************************************************** 
// 一键协程化函数原型
//*********************************************************** 
// 被 Hook 的函数需要在协程容器中使用
// *通过 flags 设置要 Hook 的函数的范围*
Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]); // v4.4+版本使用此方法。
// 或
Swoole\Runtime::enableCoroutine($flags = SWOOLE_HOOK_ALL);
// *同时开启多个 flags 需要使用 | 操作*
Co::set(['hook_flags'=> SWOOLE_HOOK_TCP | SWOOLE_HOOK_SLEEP]);
// *Swoole\Runtime::enableCoroutine() 和 Co::set(['hook_flags']) 用哪个*
// - Swoole\Runtime::enableCoroutine()，可以在服务启动后 (运行时) 动态设置 flags，调用方法后当前进程内全局生效，应该放在整个项目开始以获得 100% 覆盖的效果；
// - Co::set() 可以理解为 PHP 的 ini_set()，需要在 Server->start() 前或 Co\run() 前调用，否则设置的 hook_flags 不会生效，在 v4.4+ 版本应该用此种方式设置 flags；
// - 无论是 Co::set(['hook_flags']) 还是 Swoole\Runtime::enableCoroutine() 都应该只调用一次，重复调用会被覆盖。


//*********************************************************** 
// 协程化 flags 支持的选项
//*********************************************************** 
// SWOOLE_HOOK_ALL
// 打开下述所有类型的 flags (不包括 CURL)
Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);//不包括CURL
Co::set(['hook_flags' => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL]);//真正的hook所有类型，包括CURL

// SWOOLE_HOOK_TCP
// v4.1 开始支持，TCP Socket 类型的 stream，包括最常见的 Redis、PDO、Mysqli 以及用 PHP 的 streams 系列函数操作 TCP 连接的操作，都可以 Hook，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_TCP]);
Co\run(function() {
    for ($c = 100; $c--;) {
        go(function () {//创建100个协程
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            $redis->get('key');//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
        });
    }
});

// SWOOLE_HOOK_UNIX
// v4.2 开始支持。Unix Stream Socket 类型的 stream，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_UNIX]);
Co\run(function () {
    $socket = stream_socket_server(
        'unix://swoole.sock',
        $errno,
        $errstr,
        STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
    );
    if (!$socket) {
        echo "$errstr ($errno)" . PHP_EOL;
        exit(1);
    }
    while (stream_socket_accept($socket)) {
    }
});

// SWOOLE_HOOK_UDP
// v4.2 开始支持。UDP Socket 类型的 stream，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_UDP]);
Co\run(function () {
    $socket = stream_socket_server(
        'udp://0.0.0.0:6666',
        $errno,
        $errstr,
        STREAM_SERVER_BIND
    );
    if (!$socket) {
        echo "$errstr ($errno)" . PHP_EOL;
        exit(1);
    }
    while (stream_socket_recvfrom($socket, 1, 0)) {
    }
});

// SWOOLE_HOOK_UDG
// v4.2 开始支持。Unix Dgram Socket 类型的 stream，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_UDG]);
Co\run(function () {
    $socket = stream_socket_server(
        'udg://swoole.sock',
        $errno,
        $errstr,
        STREAM_SERVER_BIND
    );
    if (!$socket) {
        echo "$errstr ($errno)" . PHP_EOL;
        exit(1);
    }
    while (stream_socket_recvfrom($socket, 1, 0)) {
    }
});

// SWOOLE_HOOK_SSL
// v4.2 开始支持。SSL Socket 类型的 stream，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_SSL]);
Co\run(function () {
    $host = 'host.domain.tld';
    $port = 1234;
    $timeout = 10;
    $cert = '/path/to/your/certchain/certchain.pem';
    $context = stream_context_create(
        array(
            'ssl' => array(
                'local_cert' => $cert,
            )
        )
    );
    if ($fp = stream_socket_client(
        'ssl://' . $host . ':' . $port,
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    )) {
        echo "connected\n";
    } else {
        echo "ERROR: $errno - $errstr \n";
    }
});

// SWOOLE_HOOK_TLS
// v4.2 开始支持。TLS Socket 类型的 stream，参考。
Co::set(['hook_flags' => SWOOLE_HOOK_TLS]);

// SWOOLE_HOOK_SLEEP
// v4.2 开始支持。sleep 函数的 Hook，包括了 sleep、usleep、time_nanosleep、time_sleep_until，由于底层的定时器最小粒度是 1ms，
// 因此使用 usleep 等高精度睡眠函数时，如果设置为低于 1ms 时，将直接使用 sleep 系统调用。可能会引起非常短暂的睡眠阻塞。
Co::set(['hook_flags' => SWOOLE_HOOK_SLEEP]);
Co\run(function () {
    go(function () {
        sleep(1);
        echo '1' . PHP_EOL;
    });
    go(function () {
        echo '2' . PHP_EOL;
    });
});

// SWOOLE_HOOK_FILE
// v4.3 开始支持。
// 文件操作的 Hook，支持的函数有：
// fopen
// fread/fgets
// fwrite/fputs
// file_get_contents、file_put_contents
// unlink
// mkdir
// rmdir
Co::set(['hook_flags' => SWOOLE_HOOK_FILE]);
Co\run(function () {
    $fp = fopen("test.log", "a+");
    fwrite($fp, str_repeat('A', 2048));
    fwrite($fp, str_repeat('B', 2048));
});

// SWOOLE_HOOK_STREAM_FUNCTION
// v4.4 开始支持。stream_select() 的 Hook:
Co::set(['hook_flags' => SWOOLE_HOOK_STREAM_FUNCTION]);

Co\run(function () {
    $fp1 = stream_socket_client("tcp://www.baidu.com:80", $errno, $errstr, 30);
    $fp2 = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
    if (!$fp1) {
        echo "$errstr ($errno) \n";
    } else {
        fwrite($fp1, "GET / HTTP/1.0\r\nHost: www.baidu.com\r\nUser-Agent: curl/7.58.0\r\nAccept: */*\r\n\r\n");
        $r_array = [$fp1, $fp2];
        $w_array = $e_array = null;
        $n = stream_select($r_array, $w_array, $e_array, 10);
        $html = '';
        while (!feof($fp1)) {
            $html .= fgets($fp1, 1024);
        }
        fclose($fp1);
    }
});

// SWOOLE_HOOK_BLOCKING_FUNCTION
// v4.4 开始支持。这里的 blocking function 包括了：gethostbyname、exec、shell_exec，示例:
Co::set(['hook_flags' => SWOOLE_HOOK_BLOCKING_FUNCTION]);
Co\run(function () {
    while (true) {
        exec("cat");
    }
});

// SWOOLE_HOOK_PROC
// v4.4 开始支持。Hook proc* 函数，包括了：proc_open、proc_close、proc_get_status、proc_terminate。