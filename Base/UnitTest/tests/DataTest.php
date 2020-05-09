<?php
namespace app\test;

use Iterator;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @dataProvider additionKeyProvider
     */
    public function testAdd($a, $b, $excepted)
    {
        $this->assertSame($excepted, $a + $b);
    }

    public function additionProvider()
    {
        return [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 3],
        ];
    }

    public function additionKeyProvider()
    {
        return [
            'adding zeros' => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one' => [1, 1, 3],
        ];
    }

    /**
     * @dataProvider additionCsvProvider
     */
    public function testCsvAdd($a, $b, $excepted)
    {
        $this->assertSame($excepted, $a + $b);
    }

    public function additionCsvProvider()
    {
        return new CsvFileIterator('data.csv');
    }
}

class CsvFileIterator implements Iterator
{
    protected $file;
    protected $key = 0;
    protected $current;

    public function __construct(string $file) {
        $this->file = fopen(__DIR__.DIRECTORY_SEPARATOR.$file, 'r');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function rewind()
    {
        rewind($this->file);
        $this->current = array_map(function ($item){
            return intval($item);
        }, fgetcsv($this->file)); 
        $this->key = 0;
    }

    public function valid()
    {
        return !feof($this->file);
    }

    public function key()
    {
        return $this->key;
    }

    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $this->current = array_map(function ($item){
            return intval($item);
        }, fgetcsv($this->file)); 
        $this->key++;
    }
}