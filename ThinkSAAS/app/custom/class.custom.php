<?php
defined('IN_TS') or die('Access Denied.');

class custom extends tsApp 
{
    public function __construct($db)
    {
        $tsAppDb = [];
        include 'app/custom/config.php';
        // 判断APP是否采用独立数据库
        if ($tsAppDb) {
            $db = new MySql($tsAppDb);
        }
        parent::__construct($db);
    }
}