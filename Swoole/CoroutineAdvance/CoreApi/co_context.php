<?php
// 获取当前协程的上下文对象。
function func(callable $fn, ...$args)
{
    go(function () use ($fn, $args) {
        $fn(...$args);
        echo "Coroutine#".Co::getCid()." exit".PHP_EOL;
    });
}

/**
 * 兼容低版本
 *
 * @param object|resource $object
 * @return int
 */
function php_object_id($object)
{
    static $id = 0;
    static $map = [];
    $hash = spl_object_hash($object);
    return $map[$hash] ?? ($map[$hash] = ++$id);
}

class Resource
{
    public function __construct()
    {
        echo __CLASS__.'#'.php_object_id((object)$this).' constructed'.PHP_EOL; 
    }

    public function __destruct()
    {
        echo __CLASS__.'#'.php_object_id((object)$this).' destructed'.PHP_EOL; 
    }
}

$context = new Co\Context();
assert($context instanceof ArrayObject);
assert(Co::getContext() === null);
func(function () {
    $context = Co::getContext();
    assert($context instanceof Co\Context);
    $context['resource1'] = new Resource;
    $context->recource2 = new Resource;
    func(function () {
        Co::getContext()['recource3'] = new Resource;
        Co::yield();
        Co::getContext()['recource3']->recource4 = new Resource;
        Co::getContext()->recource5 = new Resource;
    });
});
Co::resume(2);