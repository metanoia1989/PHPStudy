<?php
// 可以使用一个 Context 类来管理协程上下文，在 Context 类中，使用 Coroutine::getUid 获取了协程 ID，然后隔离不同协程之间的全局变量，协程退出时清理上下文数据

use Swoole\Coroutine;

class Context 
{
    protected static $pool = [];

    static function get($key) 
    {
        $cid = Coroutine::getuid();
        if ($cid < 0) {
            return null;
        }
        if (isset(self::$pool[$cid][$key])) {
            return self::$pool[$cid][$key];
        }
        return null;
    }

    static function put($key, $item) 
    {
        $cid = Coroutine::getuid();
        if ($cid > 0) {
            self::$pool[$cid][$key] = $item;
        }
    }

    static function delete($key = null)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0) {
            if ($key) {
                unset(self::$pool[$cid][$key]);
            } else {
                unset(self::$pool[$cid]);
            }
        }
    }
}

$server = new Swoole\Http\Server('0.0.0.0', 9501);
$server->on('request', function ($request, $response) {
    if ($request->server['request_uri'] == '/a') {
        Context::put('name', 'a');
        co::sleep(1.0);
        echo Context::get('name');
        $response->end(Context::get('name'));
        // 退出协程时清理
        Context::delete('name');
    } else {
        Context::put('name', 'b');
        $response->end(Context::get('name'));
        // 退出协程时清理
        Context::delete();
    }
});

$server->start();