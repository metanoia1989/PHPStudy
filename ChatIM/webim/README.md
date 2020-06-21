项目说明
========

这是韩天峰用Swoole写的聊天项目，项目地址 https://github.com/matyhtf/webim 

用Swoole写常驻内存应用，坑还是挺多的，毕竟只是扩展，不像Go从语言层面支持并发、协程。      

Swoole在我看来已经非常了不起，开辟了PHP之前无法涉足的后端领域，但是对其缺点也要有清醒的认识。   

这玩意把我网站搞死了哭。。。      
1.已经做好的网站不能开这个，整合你网站就废了，常驻内存没几个扩展支持的！      
2.只适合单独的聊天项目什么的，里面全是坑需要自己一个个填，指不定哪天会塌陷。      
3.常驻内存，并没提高网站并发，反而耗光内存！      
有没有单独的，没laravels这种全部整合的      


hyperf了解一下      

WebIM
========


使用`PHP`+`Swoole4`实现的网页即时聊天工具，在线体验地址：[http://webim.swoole.com/](http://webim.swoole.com/)

* 基于`Swoole4`协程实现，可以同时支持数百万`TCP`连接在线
* 基于`WebSocket`+`Http Comet`支持所有浏览器/客户端/移动端
* 支持单聊/群聊/组聊等功能
* 聊天记录使用`MySQL`存储
* 用户列表和在线信息使用`Redis`存储
* 基于`Server PUSH`的即时内容更新，登录/登出/状态变更/消息等会内容即时更新
* 支持发送链接/图片/语音/视频/文件（开发中）
* 支持`Web`端直接管理所有在线用户和群组（开发中）

依赖
----
需要`Swoole-4.4.7`或更高版本
```shell
pecl install swoole
```

部署说明
----

### 安装依赖的 Composer 包

```shell
composer install
```

### 修改配置

* 配置`configs/redis.php`中的`Redis`服务器信息，用户列表和信息会存到`Redis`中
* 配置`configs/db.php`中数据库信息，聊天记录会存储到`MySQL`中
* 导入`MySQL`表接口到对应的数据库中

表结构
```sql
CREATE TABLE `webim_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(64) COLLATE utf8mb4_bin NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(12) COLLATE utf8mb4_bin NOT NULL,
  `msg` text COLLATE utf8mb4_bin NOT NULL,
  `send_ip` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin
```

* 修改`configs/webim.php`中的选项，设置服务器的URL和端口
```php
$config['server'] = array(
    //监听的HOST
    'host' => '0.0.0.0',
    //监听的端口
    'port' => '9503',
    //配置域名 [可选]
    'name' => 'im.swoole.com',
);
```

* `server.host`，`server.port` 项为`WebIM`服务器即`WebSocket`服务器的地址与端口
* `server.name`配置使用的域名（可选），如果未设置将直接使用`IP:PORT`进行访问
* 监听`80`和`443`等`1024`以内端口需要`root`权限


### 启动服务器

```shell
php server.php
```

### 配置域名解析或者本地 Host [可选]

* 直接使用`IP:PORT`，这里不需要设置。直接打开 `http://IP:PORT/` 即可
* 外网域名需要配置`DNS`解析
* 本机域名需要修改`/etc/hosts`，增加`127.0.0.1 im.swoole.com`本机域名绑定

配置成功后，可以使用浏览器打开，如：`http://im.swoole.com:9503/`

> 以上仅为示例，实际项目需要修改为对应的域名
