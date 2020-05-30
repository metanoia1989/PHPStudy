<?php
// HTTP 服务器
// 完全协程化的 HTTP 服务器实现，Co\Http\Server 由于 HTTP 解析性能原因使用 C++ 编写，因此并非由 PHP 编写的 Co\Server 的子类。
// 与 Http\Server 的不同之处：
// - 可以在运行时动态地创建、销毁
// - 对连接的处理是在单独的子协程中完成，客户端连接的 Connect、Request、Response、Close 是完全串行的
// 需要 v4.4.0 或更高版本
// 若编译时开启 HTTP2，则默认会启用 HTTP2 协议支持，无需像 Swoole\Http\Server 一样配置 open_http2_protocol (注: v4.4.16 以下版本 HTTP2 支持存在已知 BUG, 请升级后使用)

// 短命名
// 可使用 Co\Http\Server 短名。

//***********************************************************
// Swoole\Coroutine\Http\Server 的方法
//***********************************************************
// __construct()
// Swoole\Coroutine\Http\Server->__construct(string $host, int $port = 0, bool $ssl = false, bool $reuse_port = false);

// handle()
// 注册回调函数以处理参数 $pattern 所指示路径下的 HTTP 请求。
// Swoole\Coroutine\Http\Server->handle(string $pattern, callable $fn): void;
// 必须在 Server::start 之前设置处理函数
// *提示*
// - 服务器在 Accept（建立连接）成功后，会自动创建协程并接受 HTTP 请求
// - $fn 是在新的子协程空间内执行，因此在函数内无需再次创建协程
// - 客户端支持 KeepAlive，子协程会循环继续接受新的请求，而不退出
// - 客户端不支持 KeepAlive，子协程会停止接受请求，并退出关闭连接
// *注意*
// - $pattern 设置相同路径时，新的设置会覆盖旧的设置；
// - 未设置 / 根路径处理函数并且请求的路径没有找到任何匹配的 $pattern，Swoole 将返回 404 错误；
// - $pattern 使用字符串匹配的方法，不支持通配符和正则，不区分大小写，匹配算法是前缀匹配，例如：url 是 /test111 会匹配到 /test 这个规则，匹配到即跳出匹配忽略后面的配置；
// - 推荐设置 / 根路径处理函数，并在回调函数中使用 $request->server['request_uri'] 进行请求路由。
function callback(Swoole\Http\Request $req, Swoole\Http\Response $resp) {
    $resp->end('hello world');
}

Co\run(function () {
    $server = new Co\Http\Server('0.0.0.0', 9502, false);
    $server->handle('/', function ($request, $response) {
        $response->end('<h1>Index</h1>');
    });
    $server->handle('/test', function ($request, $response) {
        $response->end('<h1>Test</h1>');
    });
    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end('<h1>Stop</h1>');
        $server->shutdown();
    });
    $server->start();
});