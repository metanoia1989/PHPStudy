<?php

namespace app\test;

use PHPUnit\Framework\TestCase;

class SomeClass
{
    public function doSomething()
    {

    }
}

class StubTest extends TestCase
{
    public function testStub()
    {
        // 为 SomeClass 类建立桩件
        $stub = $this->getMockBuilder(SomeClass::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        // 配置桩件
        $stub->method('doSomething')->willReturn('foo');

        // 调用 $stub->doSomething() 将返回 'foo'
        $this->assertEquals('foo', $stub->doSomething());

        $this->assertTrue($stub instanceof SomeClass);
    }

    public function testReturnArgumentStub()
    {
        $stub = $this->createStub(SomeClass::class);
        $stub->method('doSomething')->will($this->returnArgument(0));

        $this->assertSame('foo', $stub->doSomething('foo'));
        $this->assertSame('bar', $stub->doSomething('bar'));
    }

    public function testReturnSelf()
    {
        $stub = $this->createStub(SomeClass::class);
        $stub->method('doSomething')->will($this->returnSelf());

        $this->assertSame($stub, $stub->doSomething());
    }
}