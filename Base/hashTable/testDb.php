<?php
require "db.class.php";

$db = new DB();
$db->open("dbtest");

$start_time = explode(' ', microtime());
$start_time = $start_time[0] + $start_time[1];

for ($i = 0; $i < 10000; $i++) {
    // $db->insert("key".$i, "value".$i);
    echo $db->fetch("key$i");
}

$end_time = explode(' ', microtime());
$end_time = $end_time[0] + $end_time[1];

$db->close();

echo "process time in ".($end_time - $start_time)." seconds";