<?php

namespace app\tests;

use app\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    protected function tearDown(): void
    {
        $this->calculator = null;
    }

    public function addDataProvider()
    {
        return [
            [1, 2, 3],
            [0, 0, 0],
            [-1, -1, -2],
        ];
    }

    /**
     * @dataProvider addDataProvider
     *
     * @param integer $a
     * @param integer $b
     * @param integer $expected
     * @return void
     */
    public function testAdd($a, $b, $expected)
    {
        $result = $this->calculator->add($a, $b);
        $this->assertEquals($expected, $result);
    }
}