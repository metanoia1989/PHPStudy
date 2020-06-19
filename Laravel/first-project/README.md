# Swoole 从入门到实战
教程地址 https://xueyuanjun.com/books/swoole-tutorial           

## 安装配置 LaravelS
LaravelS 基于这个扩展包可以快速入门，免去很多不必要的配置和整合操作，你可以将其看作套在 Swoole 之上的一层壳，或者一个代理，真正提供服务的还是底层的 Swoole。        

在 Laravel 应用中使用 Swoole 之前，先通过 Composer 安装 LaravelS 扩展包：
```sh
$ composer require hhxsv5/laravel-s
$ php artisan laravels publish
$ php bin/laravels start
```

添加 nginx 反向代理：
```conf
upstream laravels {
    # Connect IP:Port
    server workspace:5200 weight=5 max_fails=3 fail_timeout=30s;
    keepalive 16;
}

server {
    listen 80;
    
    server_name lara-first.test;
    root ""E:/WorkSpace/PHPStudy/Laravel/first-project/public"";
    index index.php index.html error/index.html;
    
    # Nginx 处理静态资源，LaravelS 处理动态资源
    location / {
        try_files $uri @laravels;
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

