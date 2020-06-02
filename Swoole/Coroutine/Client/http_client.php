<?php

//*******************************************************
// 协程 HTTP/WebSocket 客户端
//*******************************************************
// 协程版 HTTP 客户端的底层用纯 C 编写，不依赖任何第三方扩展库，拥有超高的性能。
// - 支持 Http-Chunk、Keep-Alive 特性，支持 form-data 格式
// - HTTP 协议版本为 HTTP/1.1
// - 支持升级为 WebSocket 客户端
// - gzip 压缩格式支持需要依赖 zlib 库
// - 客户端仅实现核心的功能，实际项目建议使用 Saber


//*******************************************************
// Swoole\Coroutine\Http\Client 属性
//*******************************************************
// errCode
// 错误状态码。当 connect/send/recv/close 失败或者超时时，会自动设置 Swoole\Coroutine\Http\Client->errCode 的值
// Swoole\Coroutine\Http\Client->errCode: int;
// errCode 的值等于 Linux errno。可使用 socket_strerror 将错误码转为错误信息。
// - 如果connect refuse，错误码为111
// - 如果超时，错误码为110
// echo socket_strerror($client->errCode);


// body
// 存储上次请求的返回包体。
// Swoole\Coroutine\Http\Client->body: string;
Co\run(function (){
    $cli = new Swoole\Coroutine\Http\Client('192.168.0.104', 80);
    $cli->get('/');
    echo $cli->body;
    $cli->close();
});

// statusCode
// HTTP 状态码，如 200、404 等。状态码如果为负数，表示连接存在问题。查看更多
// Swoole\Coroutine\Http\Client->statusCode: int;


//*******************************************************
// Swoole\Coroutine\Http\Client 方法
//*******************************************************
// __construct()
// 构造方法。
// Swoole\Coroutine\Http\Client->__construct(string $host, int $port, bool $ssl = false);
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client('127.0.0.1', 80);
    $client->setHeaders([
        'Host' => 'localhost',
        'User-Agent' => 'Chrome/49.0.2587.3',
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ]);
    $client->set(['timeout' => 1]);
    $client->get('/index.php');
    echo $client->body;
    $client->close();
});

// set()
// 设置客户端参数。
// Swoole\Coroutine\Http\Client->set(array $options);
// 此方法与 Swoole\Client->set 接收的参数完全一致，可参考 Swoole\Client->set 方法的文档。
// Swoole\Coroutine\Http\Client 额外增加了一些选项，来控制 HTTP 和 WebSocket 客户端。
// *超时控制*
// 设置 timeout 选项，启用 HTTP 请求超时检测。单位为秒，最小粒度支持毫秒。
$http->set(['timeout' => 3.0]);
// - 连接超时或被服务器关闭连接，statusCode 将设置为 -1
// - 在约定的时间内服务器未返回响应，请求超时，statusCode 将设置为 -2
// - 请求超时后底层会自动切断连接
// - 设置为 -1 表示永不超时，底层将不会添加超时检测的定时器
// - 参考客户端超时规则
// *keep_alive*
// 设置 keep_alive 选项，启用或关闭 HTTP 长连接。
$http->set(['keep_alive' => false]);
// *websocket_mask*
// 由于 RFC 规定，v4.4.0 后此配置默认开启，但会导致性能损耗，如服务器端无强制要求可以设置 false 关闭
// WebSocket 客户端启用或关闭掩码。默认为关闭。启用后会对 WebSocket 客户端发送的数据使用掩码进行数据转换。
$http->set(['websocket_mask' => true]);
// *websocket_compression*
// 需要 v4.4.12 或更高版本
// 为 true 时允许对帧进行 zlib 压缩，具体是否能够压缩取决于服务端是否能够处理压缩（根据握手信息决定，参见 RFC-7692） 
// 需要配合 flags 参数 SWOOLE_WEBSOCKET_FLAG_COMPRESS 来真正地对具体的某个帧进行压缩，具体使用方法见此节

// setMethod()
// 设置请求方法。仅在当前请求有效，发送请求后会立刻清除 method 设置。
// Swoole\Coroutine\Http\Client->setMethod(string $method): void;

// setHeaders()
// 设置 HTTP 请求头。
// Swoole\Coroutine\Http\Client->setHeaders(array $headers): void;

// setCookies()
// 设置 Cookie, 值将会被进行 urlencode 编码，若想保持原始信息，请自行用 setHeaders 设置名为 Cookie 的 header。
// Swoole\Coroutine\Http\Client->setCookies(array $cookies): void;
// - 设置 COOKIE 后在客户端对象存活期间会持续保存
// - 服务器端主动设置的 COOKIE 会合并到 cookies 数组中，可读取 $client->cookies 属性获得当前 HTTP 客户端的 COOKIE 信息
// - 重复调用 setCookies 方法，会覆盖当前的 Cookies 状态，这会丢弃之前服务器端下发的 COOKIE 以及之前主动设置的 COOKIE

// setData()
// 设置 HTTP 请求的包体。
// Swoole\Coroutine\Http\Client->setData(string|array $data): void;
// 提示
// - 设置 $data 后并且未设置 $method，底层会自动设置为 POST
// - 如果 $data 为数组时且 Content-Type 为 urlencoded 格式，底层将会自动进行 http_build_query
// - 如果使用了 addFile 或 addData 导致启用了 form-data 格式，$data 值为字符串时将会被忽略 (因为格式不同), 但为数组时底层将会以 form-data 格式追加数组中的字段

// addFile()
// 添加 POST 文件。
// 使用 addFile 会自动将 POST 的 Content-Type 将变更为 form-data。addFile 底层基于 sendfile，可支持异步发送超大文件。
// Swoole\Coroutine\Http\Client->addFile(string $path, string $name,string $mimeType = null, string $filename = null, int $offset = 0, int $length = 0): void;
Co\run(function () {
    $cli = new Swoole\Coroutine\Http\Client('httpbin.org', 80);
    $cli->setHeaders([
        'Host' => 'httpbin.org'
    ]);
    $cli->set(['timeout' => -1]);
    $cli->addFile(__FILE__, 'file1', 'text/plain');
    $cli->post('/post', ['foo' => 'bar']);
    echo $cli->body;
    $cli->close();
});

// addData()
// 使用字符串构建上传文件内容。
// addData 在 v4.1.0 以上版本可用
// Swoole\Coroutine\Http\Client->addData(string $data, string $name, string $mimeType = null, string $filename = null): void
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client('httpbin.org', 80);
    $client->setHeaders([
        'Host' => 'httpbin.org'
    ]);
    $client->set(['timeout' => -1]);
    $client->addData(Co::readFile(__FILE__), 'file1', 'text/plain');
    $client->post('/post', ['foo' => 'bar']);
    echo $client->body;
    $client->close();
});

// get()
// 发起 GET 请求。
// Swoole\Coroutine\Http\Client->get(string $path): void
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client('127.0.0.1', 80);
    $client->setHeaders([
        'Host' => 'localhost',
        'User-Agent' => 'Chrome/49.0.2587.3',
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ]);
    $client->get('/index.php');
    echo $client->body;
    $client->close();
});

// post()
// 发起 POST 请求。
// Swoole\Coroutine\Http\Client->post(string $path, mixed $data): void
// 如果 $data 为数组底层自动会打包为 x-www-form-urlencoded 格式的 POST 内容，并设置 Content-Type 为 application/x-www-form-urlencoded
// 使用 post 会忽略 setMethod 设置的请求方法，强制使用 POST
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client('127.0.0.1', 80);
    $client->post('/post.php', array('a' => '123', 'b' => '456'));
    echo $client->body;
    $client->close();
});

// upgrade()
// 升级为 WebSocket 连接。
// Swoole\Coroutine\Http\Client->upgrade(string $path): bool
// 提示
// - 某些情况下请求虽然是成功的，upgrade 返回了 true，但服务器并未设置 HTTP 状态码为 101，而是 200 或 403，这说明服务器拒绝了握手请求
// - WebSocket 握手成功后可以使用 push 方法向服务器端推送消息，也可以调用 recv 接收消息
// - upgrade 会产生一次协程调度
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client("127.0.0.1", 9501);
    $ret = $client->upgrade("/");
    if ($ret) {
        while(true) {
            $client->push("hello");
            var_dump($client->recv());
            co::sleep(0.1);
        }
    }
});

// push()
// 向 WebSocket 服务器推送消息。
// push 方法必须在 upgrade 成功之后才能执行
// push 方法不会产生协程调度，写入发送缓存区后会立即返回
// Swoole\Coroutine\Http\Client->push(mixed $data, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true): bool

// recv()
// 接收消息。只为 websocket 使用，需要配合 upgrade () 使用，见示例
// Swoole\Coroutine\Http\Client->recv(float $timeout = -1): void
// 设置超时，优先使用指定的参数，其次使用 set 方法中传入的 timeout 配置，参考客户端超时规则
Co\run(function () {
    $client = new Swoole\Coroutine\Http\Client("127.0.0.1", 9501);
    $ret = $client->upgrade("/");
    if ($ret) {
        while(true) {
            $client->push("hello");
            var_dump($client->recv());
            co::sleep(0.1);
        }
    }
});

// download()
// 通过 HTTP 下载文件。
// download 与 get 方法的不同是 download 收到数据后会写入到磁盘，而不是在内存中对 HTTP Body 进行拼接。因此 download 仅使用小量内存，就可以完成超大文件的下载。
// Swoole\Coroutine\Http\Client->download(string $path, string $filename,  int $offset = 0): bool
// 返回值
// - 执行成功返回 true
// - 打开文件失败或底层 fseek() 文件失败返回 false
Co\run(function () {
    $host = 'www.swoole.com';
    $client = new \Swoole\Coroutine\Http\Client($host, 443, true);
    $client->set(['timeout' => -1]);
    $client->setHeaders([
        'Host' => $host,
        'User-Agent' => 'Chrome/49.0.2587.3',
        'Accept' => '*',
        'Accept-Encoding' => 'gzip'
    ]);
    $client->download('/static/files/swoole-logo.svg', __DIR__ . '/logo.svg');
});

// getCookies()
// 获取 HTTP 响应的 cookie 内容。
// Swoole\Coroutine\Http\Client->getCookies(): array|false;
// Cookie 信息将经过 urldecode 解码，想要获取原始 Cookie 信息请按照下文自行解析
// 获取重名 Cookie 或 Cookie 原始头信息
var_dump($client->set_cookie_headers);

// getHeaders()
// 返回 HTTP 响应的头信息。
// Swoole\Coroutine\Http\Client->getHeaders(): array|false;

// getStatusCode()
// 获取 HTTP 响应的状态码。
// Swoole\Coroutine\Http\Client->getStatusCode(): int|false;
// 提示
// 状态码如果为负数，表示连接存在问题。
// 状态码	v4.2.10 以上版本对应常量	说明
// -1	SWOOLE_HTTP_CLIENT_ESTATUS_CONNECT_FAILED	连接超时，服务器未监听端口或网络丢失，可以读取 $errCode 获取具体的网络错误码
// -2	SWOOLE_HTTP_CLIENT_ESTATUS_REQUEST_TIMEOUT	请求超时，服务器未在规定的 timeout 时间内返回 response
// -3	SWOOLE_HTTP_CLIENT_ESTATUS_SERVER_RESET	客户端请求发出后，服务器强制切断连接

// getBody()
// 获取 HTTP 响应的包体内容。
// Swoole\Coroutine\Http\Client->getBody(): string|false;

// close()
// 关闭连接。
// Swoole\Coroutine\Http\Client->close(): bool;
// close 后如果再次请求 get、post 等方法时，Swoole 会帮你重新连接服务器。

// execute()
// 更底层的 HTTP 请求方法，需要代码中调用 setMethod 和 setData 等接口设置请求的方法和数据。
// Swoole\Coroutine\Http\Client->execute(string $path): bool;
Co\run(function(){
    $httpClient = new Swoole\Coroutine\Http\Client('httpbin.org', 80);
    $httpClient->setMethod("POST");
    $httpClient->setData("swoole");
    $status = $httpClient->execute("/post");
    var_dump($status);
    var_dump($httpClient->getBody());
});