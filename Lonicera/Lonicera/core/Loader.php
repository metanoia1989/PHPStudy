<?php
namespace Lonicera\core;

class Loader
{
    public static function loadClass()
    {

    }

    public static function loadLibClass($class)
    {
        var_dump($class);
        echo $classFile = _ROOT.$class.'.php';
        require_once $classFile;
    }
}