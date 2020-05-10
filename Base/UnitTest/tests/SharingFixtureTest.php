<?php

namespace app\tests;

use PHPUnit\Framework\TestCase;

class SharingFixtureTest extends TestCase
{
    protected static $dbh;

    public static function setUpBeforeClass(): void
    {
        self::$dbh = new \PDO('sqlite::memory:');        
    }

    public static function tearDownAfterClass(): void
    {
        self::$dbh = null; 
    }
}