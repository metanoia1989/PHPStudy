<?php
// HTTP SERVER 配置

// upload_tmp_dir
// 设置上传文件的临时目录。目录最大长度不得超过 220 字节
$server->set(array(
    'upload_tmp_dir' => '/data/uploadfiles/',
));

// http_parse_post
// 针对 Request 对象的配置，设置 POST 消息解析开关，默认开启
// 设置为 true 时自动将 Content-Type为x-www-form-urlencoded 的请求包体解析到 POST 数组。
// 设置为 false 时将关闭 POST 解析。
$server->set(array(
    'http_parse_post' => false,
));

// http_parse_cookie
// 针对 Request 对象的配置，关闭 Cookie 解析，将在 header 中保留未经处理的原始的 Cookies 信息。默认开启
$server->set(array(
    'http_parse_cookie' => false,
));

// http_compression
// 针对 Response 对象的配置，启用压缩。默认为开启。
// - http-chunk 不支持分段单独压缩，若使用 write 方法，将会强制关闭压缩。
// - http_compression 在 v4.1.0 或更高版本可用
$server->set(array(
    'http_compression' => false,
));
// 目前支持 gzip、br、deflate 三种压缩格式，底层会根据浏览器客户端传入的 Accept-Encoding 头自动选择压缩方式。
// 依赖：
// gzip 和 deflate 依赖 zlib 库，在编译 Swoole 时底层会检测系统是否存在 zlib。
// 可以使用 yum 或 apt-get 安装 zlib 库：
// sudo apt-get install libz-dev
// br 压缩格式依赖 google 的 brotli 库，安装方式请自行搜索 install brotli on linux，在编译 Swoole 时底层会检测系统是否存在 brotli。

// http_compression_level
// 压缩级别，针对 Response 对象的配置
// $level 压缩等级，范围是 1-9，等级越高压缩后的尺寸越小，但 CPU 消耗更多。默认为 1, 最高为 9

// document_root
// 配置静态文件根目录，与 enable_static_handler 配合使用。
// 此功能较为简易，请勿在公网环境直接使用
// - 设置 document_root 并设置 enable_static_handler 为 true 后，底层收到 Http 请求会先判断 document_root 路径下是否存在此文件，如果存在会直接发送文件内容给客户端，不再触发 onRequest 回调。
// - 使用静态文件处理特性时，应当将动态 PHP 代码和静态文件进行隔离，静态文件存放到特定的目录
$server->set([
    'document_root' => '/data/webroot/example.com', // v4.4.0以下版本, 此处必须为绝对路径
    'enable_static_handler' => true,
]);

// enable_static_handler
// 开启静态文件请求处理功能，需配合 document_root 使用 默认 false

// http_autoindex
// 开启 http autoindex 功能 默认不开启

// http_index_files
// 配合 http_autoindex 使用，指定需要被索引的文件列表
$server->set([
    'document_root' => '/data/webroot/example.com',
    'enable_static_handler' => true,
    'http_autoindex' => true,
    'http_index_files' => ['indesx.html', 'index.txt'],
]);

// static_handler_locations
// 设置静态处理器的路径。类型为数组，默认不启用。
// Swoole >= v4.4.0
// - 类似于 Nginx 的 location 指令，可以指定一个或多个路径为静态路径。只有 URL 在指定路径下才会启用静态文件处理器，否则会视为动态请求。
// - location 项必须以 / 开头
// - 支持多级路径，如 /app/images
// - 启用 static_handler_locations 后，如果请求对应的文件不存在，将直接返回 404 错误
$server->set([
    "static_handler_locations" => ['/static', '/app/images'],
]);

// open_http2_protocol
// 启用 HTTP2 协议解析【默认值：false】
// 注意
// 需要编译时启用 --enable-http2 选项