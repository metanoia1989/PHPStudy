<?php
class A
{
    public static function call()
    {
        echo "class A<br>";
    }

    public static function test()
    {
        self::call();
        static::call();
    }
}

class B extends A
{
    public static function call()
    {
        echo "class B.<br>";
    }
}

B::test();
