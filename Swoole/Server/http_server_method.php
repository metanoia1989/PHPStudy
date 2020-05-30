<?php
// Swoole Server的事件监听，多次监听时，后注册的回调函数将会覆盖之前注册的。        
// 会覆盖，也就意味着整个应用的路由分发，都会只在 onRequest 事件中。

// Http\Server
// Http\Server 对 HTTP 协议的支持并不完整，一定要作为应用服务器处理动态请求。并且在前端增加 Nginx 作为代理
// Http\Server 继承自 Server，所以 Server 提供的所有 API 和配置项都可以使用，进程模型也是一致的。请参考 Server 章节。
// 内置 HTTP 服务器的支持，通过几行代码即可写出一个高并发，高性能，异步 IO 的多进程 HTTP 服务器。
$http_server = new Swoole\Http\Server("127.0.0.1", 9501);
$http_server->on('request', function ($request, $response) {
    $response->end("<h1>first response：Hello Swoole. #".rand(1000, 9999)."</h1>");
});
// *使用 HTTP2 协议*
// 使用 SSL 下的 HTTP2 协议必须安装 openssl, 且需要高版本 openssl 必须支持 TLS1.2、ALPN、NPN
// 编译时需要使用 --enable-http2 开启
// ./configure --enable-openssl --enable-http2
// Copy to clipboardErrorCopied
// 设置 HTTP 服务器的 open_http2_protocol 为 true
// $server = new Swoole\Http\Server("127.0.0.1", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
// $server->set([
//     'ssl_cert_file' => $ssl_dir . '/ssl.crt',
//     'ssl_key_file' => $ssl_dir . '/ssl.key',
//     'open_http2_protocol' => true,
// ]);
// nginx+swoole 配置
// server {
//     root /data/wwwroot/;
//     server_name local.swoole.com;

//     location / {
//         proxy_http_version 1.1;
//         proxy_set_header Connection "keep-alive";
//         proxy_set_header X-Real-IP $remote_addr;
//         if (!-e $request_filename) {
//              proxy_pass http://127.0.0.1:9501;
//         }
//     }
// }
// 通过读取 $request->header['x-real-ip'] 来获取客户端的真实 IP

// on()
// 注册事件回调函数。
// 与 Server 的回调 相同，不同之处是：
// - Http\Server->on 不接受 onConnect/onReceive 回调设置
// - Http\Server->on 额外接受 1 种新的事件类型 onRequest
// onRequest 事件
// 在收到一个完整的 HTTP 请求后，会回调此函数。回调函数共有 2 个参数：
// - $request，HTTP 请求信息对象，包含了 header/get/post/cookie 等相关信息
// - $response，HTTP 响应对象，支持 cookie/header/status 等 HTTP 操作
// - 在 onRequest 回调函数返回时底层会销毁 $request 和 $response 对象
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
     $response->end("<h1>second listen：hello swoole</h1>");
});

// start()
// 启动 HTTP 服务器
// 启动后开始监听端口，并接收新的 HTTP 和 WebSocket 请求。
// Swoole\Http\Server->start();

//********************************************************************
// Http\Request HTTP请求对象
//********************************************************************

// ****Http\Request****
// HTTP 请求对象，保存了 HTTP 客户端请求的相关信息，包括 GET、POST、COOKIE、Header 等。
// 请勿使用 & 符号引用 Http\Request 对象

// *header*
// HTTP 请求的头部信息。类型为数组，所有 key 均为小写。
// Swoole\Http\Request->header: array;
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "输出请求头 header：\n";
    echo "Host ".$request->header['host']."\n";
    echo "Accept-Lanuage ".$request->header['accept-language']."\n";
    $response->end("<h1>third listen: hello swoole</h1>");
});

// *server*
// HTTP 请求相关的服务器信息。
// 相当于 PHP 的 $_SERVER 数组。包含了 HTTP 请求的方法，URL 路径，客户端 IP 等信息。
// Swoole\Http\Request->server: array;
// 数组的 key 全部为小写，并且与 PHP 的 $_SERVER 数组保持一致
// key	说明
// query_string	请求的 GET 参数，如：id=1&cid=2 如果没有 GET 参数，该项不存在
// request_method	请求方法，GET/POST 等
// request_uri	无 GET 参数的访问地址，如 /favicon.ico
// path_info	同 request_uri
// request_time	request_time 是在 Worker 设置的，在 SWOOLE_PROCESS 模式下存在 dispatch 过程，因此可能会与实际收包时间存在偏差。
//    尤其是当请求量超过服务器处理能力时，request_time 可能远滞后于实际收包时间。可以通过 $server->getClientInfo 方法获取 last_time 获得准确的收包时间。
// request_time_float	请求开始的时间戳，以微秒为单位，float 类型，如 1576220199.2725
// server_protocol	服务器协议版本号，HTTP 是：HTTP/1.0 或 HTTP/1.1，HTTP2 是：HTTP/2
// server_port	服务器监听的端口
// remote_port	客户端的端口
// remote_addr	客户端的 IP 地址
// master_time	连接上次通讯时间
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "HTTP 请求相关的服务器信息 $_SERVER";
    echo "query_string：".$request->server['query_string']."\n";
    echo "request_method：".$request->server['request_method']."\n";
    echo "request_uri：".$request->server['request_uri']."\n";
    echo "path_info：".$request->server['path_info']."\n";
    echo "request_time：".$request->server['request_time']."\n";
    echo "request_time_float：".$request->server['request_time_float']."\n";
    echo "server_protocol：".$request->server['server_protocol']."\n";
    echo "server_port：".$request->server['server_port']."\n";
    echo "remote_port：".$request->server['remote_port']."\n";
    echo "remote_addr：".$request->server['remote_addr']."\n";
    echo "master_time：".$request->server['master_time']."\n";
    $response->end("<h1>get request->server</h1>");
});

// *get*
// HTTP 请求的 GET 参数，相当于 PHP 中的 $_GET，格式为数组。
// Swoole\Http\Request->get: array;
// 注意
// 为防止 HASH 攻击，GET 参数最大不允许超过 128 个
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "GET参数信息\n";
    echo json_encode($request->get)."\n";
    $response->end("<h1>get request->get</h1>");
});

// *post*
// HTTP POST 参数，格式为数组
// Swoole\Http\Request->post: array;
// 注意
// - POST 与 Header 加起来的尺寸不得超过 package_max_length 的设置，否则会认为是恶意请求
// - POST 参数的个数最大不超过 128 个
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "POST参数信息\n";
    echo json_encode($request->get)."\n";
    $response->end("<h1>http->post </h1>".json_encode($request->post));
});

// *cookie*
// HTTP 请求携带的 COOKIE 信息，格式为键值对数组。
// Swoole\Http\Request->cookie: array;
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "Cookie参数信息\n";
    echo json_encode($request->cookie)."\n";
    $cookie = json_encode($request->cookie);
    $response->end("<h1>http->cookie </h1>"."<code>$cookie</code>");
});

// *files*
// 上传文件信息。
// 类型为以 form 名称为 key 的二维数组。与 PHP 的 $_FILES 相同。最大文件尺寸不得超过 package_max_length 设置的值。请勿使用 Swoole\Http\Server 处理大文件上传。
// Swoole\Http\Request->files: array;
// 注意
// 当 $request 对象销毁时，会自动删除上传的临时文件
// 示例
// Array
// (
//     [name] => facepalm.jpg // 浏览器上传时传入的文件名称
//     [type] => image/jpeg // MIME类型
//     [tmp_name] => /tmp/swoole.upfile.n3FmFr // 上传的临时文件，文件名以/tmp/swoole.upfile开头
//     [size] => 15476 // 文件尺寸
//     [error] => 0
// )
// =_= 试过 根本不行啊  带有文件的请求，直接响应超时了
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    $files = json_encode($request->files);
    echo "files 文件上传信息\n";
    echo $files."\n";
    $response->end("<h1>http->files</h1>"."<code>$files</code>");
});

// getContent()
// Swoole 版本 >= v4.5.0 可用，在低版本可使用别名 rawContent (此别名将永久保留，即向下兼容)
// 获取原始的 POST 包体。
// 用于非 application/x-www-form-urlencoded 格式的 HTTP POST 请求。返回原始 POST 数据，此函数等同于 PHP 的 fopen('php://input')
// Swoole\Http\Request->rawContent(): string;
// 提示
// 有些情况下服务器不需要解析 HTTP POST 请求参数，通过 http_parse_post 配置，可以关闭 POST 数据解析。
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    $post = json_encode($request->rawContent());
    echo "原始 Post 数据\n";
    echo $post."\n";
    $response->end("<h1>http->rawContent()</h1>"."<code>$post</code>");
});

// getData()
// 获取完整的原始 Http 请求报文。包括 Http Header 和 Http Body
// Swoole\Http\Request->getData(): string;
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    $message = json_encode($request->getData());
    echo "原始 Http 请求报文数据\n";
    echo $message."\n";
    $response->end("<h1>http->getData()</h1>"."<code>$message</code>");
});

//********************************************************************
// Http\Response HTTP响应对象
//********************************************************************
// Http\Response
// HTTP 响应对象，通过调用此对象的方法，实现 HTTP 响应发送。
// 当 Response 对象销毁时，如果未调用 end 发送 HTTP 响应，底层会自动执行 end("");
// 请勿使用 & 符号引用 Http\Response 对象

// header()
// 设置 HTTP 响应的 Header 信息
// Swoole\Http\Response->header(string $key, string $value, bool $ucwords = true);
// 注意
// - header 设置必须在 end 方法之前 -$key 必须完全符合 HTTP 的约定，每个单词首字母大写，不得包含中文，下划线或者其他特殊字符
// - $value 必须填写
// - $ucwords 设为 true，底层会自动对 $key 进行约定格式化
// - 重复设置相同 $key 的 HTTP 头会覆盖，取最后一次。
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "设置HTTP响应头\n";
    $response->header('content-type', 'application/json');
    $result = [
        "key1" => "php",
        "key2" => "javascript",
        "key3" => "c++",
        "key4" => "python",
    ];
    $response->end(json_encode($result));
});

// trailer()
// 将 Header 信息附加到 HTTP 响应的末尾，仅在 HTTP2 中可用，用于消息完整性检查，数字签名等。
// Swoole\Http\Response->trailer(string $key, string $value, bool $ucwords = true);
// *返回值*
// - 设置失败，返回 false
// - 设置成功，没有任何返回值
// 注意
// 重复设置相同 $key 的 Http 头会覆盖，取最后一次。
if (false) {
    $response->trailer('grpc-status', 0);
    $response->trailer('grpc-message', '');
}

// cookie()
// 设置 HTTP 响应的 cookie 信息。此方法参数与 PHP 的 setcookie 完全一致。
// Swoole\Http\Response->cookie(string $key, string $value = '', int $expire = 0 , string $path = '/', string $domain  = '', bool $secure = false , bool $httponly = false, string $samesite = '');
// *注意*
// - cookie 设置必须在 end 方法之前
// - $samesite 参数从 v4.4.6 版本开始支持
// - Swoole 会自动会对 $value 进行 urlencode 编码，可使用 rawCookie() 方法关闭对 $value 的编码处理
// - Swoole 允许设置多个相同 $key 的 COOKIE

// rawCookie()
// 设置 HTTP 响应的 cookie 信息
// rawCookie() 的参数和上文的 cookie() 一致，只不过不进行编码处理

// status()
// 发送 Http 状态码。
// Swoole\Http\Response->status(int $http_status_code, int $reason): bool;
// 提示
// - 如果只传入了第一个参数 $http_status_code 必须为合法的 HttpCode，如 200、502、301、404 等，否则会设置为 200 状态码
// - 如果设置了第二个参数 $reason，$http_status_code 可以为任意的数值，包括未定义的 HttpCode，如 499
// - 必须在 $response->end() 之前执行 status 方法
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "设置HTTP状态码\n";
    $response->header('content-type', 'application/json');
    $response->status(389, '资源已经被转移了，请求失败');
    $result = [
        "key1" => "php",
        "key2" => "javascript",
        "key3" => "c++",
        "key4" => "python",
    ];
    $response->end(json_encode($result));
});

// gzip()
// 此方法在 4.1.0 或更高版本中已废弃，请移步 http_compression；在新版本中使用 http_compression 配置项取代了 gzip 方法。
// 主要原因是 gzip() 方法未判断浏览器客户端传入的 Accept-Encoding 头，如果客户端不支持 gzip 压缩，强行使用会导致客户端无法解压。
// 全新的 http_compression 配置项会根据客户端 Accept-Encoding 头，自动选择是否压缩，并自动选择最佳的压缩算法。
// 启用 Http GZIP 压缩。压缩可以减小 HTML 内容的尺寸，有效节省网络带宽，提高响应时间。必须在 write/end 发送内容之前执行 gzip，否则会抛出错误。
// Swoole\Http\Response->gzip(int $level = 1);

// redirect()
// 发送 Http 跳转。调用此方法会自动 end 发送并结束响应。
// Swoole\Http\Response->redirect(string $url, int $http_code = 302): void;
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "设置重定向\n";
    $response->redirect('http://www.baidu.com', 301);
});

// write()
// 启用 Http Chunk 分段向浏览器发送相应内容。
// 关于 Http Chunk 可以参考 Http 协议标准文档。
// Swoole\Http\Response->write(string $data): bool;
// 提示
// 使用 write 分段发送数据后，end 方法将不接受任何参数，调用 end 只是会发送一个长度为 0 的 Chunk 表示数据传输完毕。
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "分段发送内容\n";
    $response->write("<h1>super man</h1>");
    $response->write("<h1>spider man</h1>");
    $response->write("<h1>iron man</h1>");
    $response->end();
});

// sendfile()
// 发送文件到浏览器。
// Swoole\Http\Response->sendfile(string $filename, int $offset = 0, int $length = 0): bool;
// 参数
// - string $filename 要发送的文件名称【文件不存在或没有访问权限 sendfile 会失败】
// - int $offset 上传文件的偏移量【可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传】
// - int $length 发送数据的尺寸
// 提示
// - 底层无法推断要发送文件的 MIME 格式因此需要应用代码指定 Content-Type
// - 调用 sendfile 前不得使用 write 方法发送 Http-Chunk
// - 调用 sendfile 后底层会自动执行 end
// - sendfile 不支持 gzip 压缩
$http_server->on('request', function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    echo "发送文件\n";
    $response->header('Content-Type', 'image/jpeg');
    $response->sendfile(__DIR__.$request->server['request_uri']);
});

// end()
// 发送 Http 响应体，并结束请求处理。
// Swoole\Http\Response->end(string $html): bool;
// 提示
// - end 只能调用一次，如果需要分多次向客户端发送数据，请使用 write 方法
// - 客户端开启了 KeepAlive，连接将会保持，服务器会等待下一次请求
// - 客户端未开启 KeepAlive，服务器将会切断连接

// detach()
// 分离响应对象。使用此方法后，$response 对象销毁时不会自动 end，与 Http\Response::create 和 Server::send 配合使用。
// detach 方法只能在 SWOOLE_PROCESS 模式下使用。
// Swoole\Http\Response->detach(): bool;
// *跨进程响应*
// 某些情况下，需要在 Task 进程中对客户端发出响应。这时可以利用 detach 使 $response 对象独立。在 Task 进程可以重新构建 $response，发起 Http 请求响应。
$http_server->set([
    'task_worker_num' => 1,
    'worker_num' => 1,
]);
$http_server->on('request', function ($req, Swoole\Http\Response $resp) use ($http_server) {
    $resp->detach();
    $http_server->task(strval($resp->fd));
});
$http_server->on('finish', function () {
    echo 'task finish\n';
});
$http_server->on('task', function ($serv, $task_id, $worker_id, $data) {
    $resp = Swoole\Http\Response::create($data);
    $resp->write("请求投递给任务进程的数据：".json_encode($data));
    $resp->end();
    echo "async task\n";
});
// *发送任意内容*
// 某些特殊的场景下，需要对客户端发送特殊的响应内容。Http\Response 对象自带的 end 方法无法满足需求，可以使用 detach 分离响应对象，然后自行组装 HTTP 协议响应数据，并使用 Server::send 发送数据。
// =_= 很难拿到响应数据啊 直接从CURL请求拿的到，并且阻塞了
// 需要手动关闭客户端连接才行。     
$http_server->on('request', function ($req, Swoole\Http\Response $resp) use ($http_server) {
    $resp->detach();
    $http_server->send($resp->fd, "HTTP/1.1 200 OK\r\nServer: server\r\n\r\nCustom Response\n");
    $http_server->close($resp->fd);
});

// create()
// 构造新的 Swoole\Http\Response 对象。
// 使用此方法前请务必调用 detach 方法将旧的 $response 对象分离，否则可能会造成对同一个请求发送两次响应内容。
// Swoole\Http\Response::create(int $fd): Swoole\Http\Response;
// 调用成功返回一个新的 Http\Response 对象，调用失败返回 false
$http_server->on('request', function ($req, Swoole\Http\Response $resp) use ($http) {
    $resp->detach();
    $resp2 = Swoole\Http\Response::create($req->fd);
    $resp2->end("手动创建新的响应对象的数据");
});

// 设置静态服务器
$http_server->set([
    'document_root' => '/mnt/e/WorkSpace/PHPStudy/Swoole/Server',
    'enable_static_handler' => true,
]);

$http_server->start();
