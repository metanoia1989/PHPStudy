<?php
class A
{
    public function call()
    {
        echo "instance from A.\n";
    }

    public function test()
    {
        self::call();
        static::call();
    }
}

class B extends A
{
    public function call()
    {
        echo "instance from B.\n";
    }
}


$b = new B();
$b->test();
