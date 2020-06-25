<?php
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

/**
 * 测试PHP的一些魔术方法
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 启动 Eloquent ORM模块并进行相关配置
$manager = new Manager();
$manager->addConnection(require __DIR__."/dbconfig.php");
$manager->bootEloquent();


class User extends Model
{
    // 定义User类并继承Model类，声明数据库表位'students'
    protected $table = 'students';
    public $timestamps = false;

    public function __toString()
    {
        return "{$this->name}: {$this->age}\n";
    }
}

$user = new User(); // 调用 __construct
$user->name = 'xiaozhang'; // 调用 __set($key, $value)
echo $user->name."\n";   // 调用 __get($key)
echo isset($user->name); // 调用 __isset($key)
unset($user->name);     // 调用 __unset($key)
$user->find(1);     // 调用 __call($method, $parameters)
$user = User::find(1);      // 调用 __callStatic($method, $parameters)
echo $user;     // 调用 __toString()
$us = serialize($user);     // 调用 __sleep()
$test2 = unserialize($us);      // 调用 __wakeup()
echo $test2;
