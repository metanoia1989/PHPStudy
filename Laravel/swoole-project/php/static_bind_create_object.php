<?php
class A
{
    public static function create()
    {
        $self = new self();
        $static = new static();
        return array($self, $static);
    }
}

class B extends A
{

}

$arr = B::create();
foreach($arr as $value) {
    var_dump($value);
}
