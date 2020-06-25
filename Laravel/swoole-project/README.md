# Swoole 从入门到实战
教程地址 https://xueyuanjun.com/books/swoole-tutorial           


## 安装配置 LaravelS
LaravelS 扩展文档 https://github.com/hhxsv5/laravel-s

LaravelS 基于这个扩展包可以快速入门，免去很多不必要的配置和整合操作，你可以将其看作套在 Swoole 之上的一层壳，或者一个代理，真正提供服务的还是底层的 Swoole。        

在 Laravel 应用中使用 Swoole 之前，先通过 Composer 安装 LaravelS 扩展包：
```sh
$ composer require hhxsv5/laravel-s
$ php artisan laravels publish
$ php bin/laravels start
```

添加 nginx 反向代理：
```conf
map $http_upgrade $connection_upgrade {
    default upgrade;
    ''      close;
}

upstream laravels {
    # Connect IP:Port
    server localhost:5200 weight=5 max_fails=3 fail_timeout=30s;
    keepalive 16;
}

server {
    listen 80;
    
    server_name lara-first.test;
    root "E:/WorkSpace/PHPStudy/Laravel/first-project/public";
    index index.php index.html error/index.html;
    
    # Nginx 处理静态资源，LaravelS 处理动态资源
    location / {
        try_files $uri @laravels;
    }

    # Http and WebSocket are concomitant, Nginx identifies them by "location"
    # !!! The location of WebSocket is "/ws"
    # Javascript: var ws = new WebSocket("ws://todo-s.test/ws");
    # 处理 WebSocket 通信
    location =/ws {
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout: Nginx will close the connection if the proxied server does not send data to Nginx in 60 seconds; At the same time, this close behavior is also affected by heartbeat setting of Swoole.
        # proxy_read_timeout 60s;
        proxy_http_version 1.1;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_pass http://localhost:5200;
    }
    
    location @laravels {
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_pass http://localhost:5200;
    }
}
```

=_= 然后启动 laravel-s 发现没有 websocket，原来是配置的时候只加了 WebSocketService 类，但是没有将 `enable` 修改为 true。        

lumen使用Hhxsv5\LaravelS实现websocket通信 https://blog.csdn.net/zhangzeshan/article/details/101852666
