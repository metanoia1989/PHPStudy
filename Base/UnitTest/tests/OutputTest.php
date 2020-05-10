<?php
namespace app\tests;

use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    public function testExpectFooActualFoo()
    {
        $this->expectOutputString('foo');
        print "foo";
    }

    public function testExpectBarActualBar()
    {
        $this->expectOutputString('bar');
        print "bar";
    }
}