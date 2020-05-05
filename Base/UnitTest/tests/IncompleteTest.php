<?php

namespace app\test;

use PHPUnit\Framework\TestCase;

class IncompleteTest extends TestCase
{
    public function testSomething()
    {
        $this->assertTrue(true, 'This should already work.');
        // 标记这个测试未完成
        $this->markTestIncomplete('This test has not been impletend yet.');
    }
}