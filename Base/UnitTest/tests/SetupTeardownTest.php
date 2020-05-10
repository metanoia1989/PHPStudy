<?php

namespace app\tests;

use PHPUnit\Framework\TestCase;

class SetupTeardownTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    protected function setUp(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    protected function assertPreConditions(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    protected function assertPostConditions(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    protected function teardown(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    public static function tearDownAfterClass(): void
    {
        fwrite(STDOUT, __METHOD__."\n");
    }

    protected function onNotSuccessfulTest(\Throwable $t): void
    {
        fwrite(STDOUT, __METHOD__."\n");
        throw $t; 
    }    

    public function testOne()
    {
        fwrite(STDOUT, __METHOD__."\n");
        $this->assertTrue(true);
    }

    public function testTwo()
    {
        fwrite(STDOUT, __METHOD__."\n");
        $this->assertTrue(false);
    }
}