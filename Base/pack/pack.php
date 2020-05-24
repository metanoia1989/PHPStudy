<?php

// pack函数使用示例
$fp = fopen("data.dat", "wb");
$bin = pack("L3", 12, 12, 12); // 写入3个长整型数据
fwrite($fp, $bin, 12);
fclose($fp);

// unpack函数使用示例
$fp = fopen("data.dat", "rb");
$bin = fread($fp, 12);
$pack = unpack("LLL", $bin);
fclose($fp);

var_dump($pack);