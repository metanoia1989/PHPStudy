<?php
namespace app\tests;

use Exception;
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

    public function testReturnValueMapStub()
    {
        $stub = $this->createStub(SomeClass::class);

        // 创建从参数到返回值的映射
        $map = [
            [ 'a', 'b', 'c', 'd' ],
            [ 'e', 'f', 'g', 'h' ],
        ];

        $stub->method('doSomething')
            ->will($this->returnValueMap($map));

        $this->assertSame('d', $stub->doSomething('a', 'b', 'c'));
        $this->assertSame('h', $stub->doSomething('e', 'f', 'g'));
    }

    public function testReturnCallbackStub()
    {
        $stub = $this->createStub(SomeClass::class);
        $stub->method('doSomething')
            ->will($this->returnCallback('str_rot13'));
        // 返回str_rot13传入参数的执行
        $this->assertSame('fbzrguvat', $stub->doSomething('something'));
    }

    public function testOnConsecutiveCallsStub()
    {
        $stub = $this->createStub(SomeClass::class);
        $stub->method('doSomething')
            ->will($this->onConsecutiveCalls(2, 3, 5, 7));

        $this->assertSame(2, $stub->doSomething());
        $this->assertSame(3, $stub->doSomething());
        $this->assertSame(5, $stub->doSomething());
    }

    public function testThrowExceptionStub()
    {
        $stub = $this->createStub(SomeClass::class);
        $stub->method('doSomething')
            ->will($this->throwException(new Exception()));
        // 将抛出异常
        $stub->doSomething();
    }
}
