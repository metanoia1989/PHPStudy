<?php
namespace app\test;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

if (!function_exists('write_test_log')) {
    /**
     * 写入测试日志
     *
     * @param string $filename
     * @param array $data
     * @return void
     */
    function write_test_log($filename = "default", $data) {
        $output = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
		file_put_contents(dirname(__DIR__)."/test/{$filename}.json", $output);	
    }
}

class Subject
{
    protected $observers = [];
    protected $name;

    public function __construct($name)
    {
        $this->name = $name; 
    }

    public function getName()
    {
        return $this->name;
    }

    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function doSomething()
    {
        // Do something

        // 通知观察者
        $this->notify('something');
    }

    public function doSomethingBad()
    {
        foreach ($this->observers as $observer) {
            $observer->reportError(42, 'Something bad happend', $this);
        }
    }

    public function notify($argument)
    {
        foreach ($this->observers as $observer) {
            $observer->update($argument);
        }
    }
}

class Observer
{
    public function update($argument)
    {
    }

    public function reportError($errorCode, $errorMessage, Subject $subject)
    {
        write_test_log('reportError', [
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
            'subject' => $subject
        ]);
    }
}

class SubjectTest extends TestCase
{
    public function testObserversAreUpdated()
    {
        // 创建Observer类的mock
        $observer = $this->createMock(Observer::class);

        // 指明交互
        // 建立预期状况：update() 方法将会被调用一次，
        // 并且将以字符串 'something' 为参数。
        $observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo('something'));

        // 创建 Subject 对象，并将mock的 Observer 对象连接其上。
        $subject = new Subject('My subject');
        $subject->attach($observer);

        // 在 $subject 对象上调用 doSomething() 方法，
        // 预期将以字符串 'something' 为参数调用
        // Observer 仿件对象的 update() 方法。
        $subject->doSomething();
    }

    // 测试某个方法将会以特定数量的参数进行调用，并且对各个参数以多种方式进行约束
    public function testErrorReported()
    {
        // 为 Observer 类建立仿件，对 reportError() 方法进行模仿
        $observer = $this->createMock(Observer::class);

        $observer->expects($this->once())
            ->method('reportError')
            ->with(
                $this->greaterThan(0),
                $this->stringContains('Something'),
                $this->callback(function($subject) {
                    return is_callable([$subject, 'getName']) && 
                        $subject->getName() == 'My subject';
                })
            );

        $subject = new Subject('My subject');
        $subject->attach($observer);

        $subject->doSomethingBad();
    }

    public function testFunctionCalledTwoTimesWithSpecificArguments()
    {
        $mock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['set'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                [$this->equalTo('foo'), $this->greaterThan(0)],
                [$this->equalTo('bar'), $this->greaterThan(0)]
            );

        $mock->set('foo', 21);
        $mock->set('bar', 21);
    }

    public function testIdenticalObjectPassed()
    {
        $exceptedObject = new stdClass();
        $mock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['foo'])
            ->getMock();

        $mock->expects($this->once())
            ->method('foo')
            ->with($this->identicalTo($exceptedObject));

        $mock->foo($exceptedObject);
    }
}