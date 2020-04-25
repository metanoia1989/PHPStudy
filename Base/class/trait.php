<?php

class Base
{
    public function hello ()
    {
        echo "method hello from class Base.\n";
    }
}

trait Hello
{
    public function hello ()
    {
        echo "method hello from Trait Hello!\n";
    }

    public function hi ()
    {
        echo "method hi from Trait Hello\n";
    }

    abstract public function getValue ();

    static public function staticMethod()
    {
        echo "static method staticMethod from Trait Hello.\n";
    }

    public function staticValue()
    {
        static $value;
        $value++;
        echo "$value\n";
    }
}

trait Hi
{
    public function hello ()
    {
        parent::hello();
        echo "method hello from Trait Hi!\n";
    }

    public function hi ()
    {
        echo "method hi from Trait Hi\n";
    }
}

trait HelloHi
{
    use Hello, Hi {
        Hello::hello insteadOf Hi;
        Hi::hi insteadOf Hello;
    }
}

class MyNew extends Base
{
    use HelloHi;

    private $value = "class MyNew\n";

    public function hi()
    {
        echo "method hi from class MyNew\n";
    }

    public function getValue()
    {
        return $this->value;
    }
}

$obj = new MyNew();
$obj->hello(); // trait中的方法覆盖了基类中的方法
$obj->hi(); // 当前类中的方法会覆盖trait方法

MyNew::staticMethod(); // trait中可以定义静态方法

echo $obj->getValue(); // trait中可以使用抽象方法

$objOther = new MyNew();
$obj->staticValue(); // trait中可以使用静态成员
$objOther->staticValue();