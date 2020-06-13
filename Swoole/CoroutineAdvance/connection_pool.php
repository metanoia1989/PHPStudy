<?php
//***************************************************************** 
// 连接池
//***************************************************************** 
// Swoole 在 v4 版本后内置了 Library 模块，使用 PHP 代码编写内核功能，使得底层设施更加稳定可靠，并且提供了内置协程连接池，本章节会说明如何使用对应的连接池。


//***************************************************************** 
// ConnectionPool
//***************************************************************** 
// ConnectionPool，原始连接池，基于 Channel 自动调度，支持传入任意构造器 (callable)，构造器需返回一个连接对象
// - get 方法获取连接（连接池未满时会创建新的连接）
// - put 方法回收连接
// - fill 方法填充连接池（提前创建连接）
// - close 关闭连接池


//***************************************************************** 
// Database
//***************************************************************** 
// 各种数据库连接池和对象代理的高级封装，支持自动断线重连。目前包含 PDO，Mysqli，Redis 三种类型的数据库支持：
// PDOConfig, PDOProxy, PDOPool
// MysqliConfig, MysqliProxy, MysqliPool
// RedisConfig, RedisProxy, RedisPool
// 1. MySQL 断线重连可自动恢复大部分连接上下文 (fetch 模式，已设置的 attribute，已编译的 Statement 等等)，但诸如事务等上下文无法恢复，若处于事务中的连接断开，将会抛出异常，请自行评估重连的可靠性；
// 2. 将处于事务中的连接归还给连接池是未定义行为，开发者需要自己保证归还的连接是可重用的；
// 3. 若有连接对象出现异常不可重用，开发者需要调用 $pool->put(null); 归还一个空连接以保证连接池的数量平衡。

// PDOPool/MysqliPool/RedisPool
// 用于创建连接池对象，存在两个参数，分别为对应的 Config 对象和连接池 size
$pool = new Swoole\Database\PDOPool($config, $size);
$pool = new Swoole\Database\MysqliPool($config, $size);
$pool = new Swoole\Database\RedisPool($config, $size);


