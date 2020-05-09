<?php

// csv文件读取测试
if(true) {
    $file = fopen("./tests/data.csv", "r");
    $current = array_map(function ($item){
        return intval($item);
    }, fgetcsv($file)); 
    var_dump($current);
    fclose($file);
}