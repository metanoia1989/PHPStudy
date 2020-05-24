<?php
require "HashTable.php";

$map = new HashTable();

$map->insert('key1', 'value1');
$map->insert('key12', 'value12');
$map->insert('key2', 'value2');
echo $map->find('key1')."\n";
echo $map->find('key12')."\n";
echo $map->find('key2')."\n";
