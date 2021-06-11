<?php

require __DIR__.'/vendor/autoload.php';

use Metanoia1989\FirstExt\Weather;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$key = $_ENV['GAODE_KEY'];

$w = new Weather($key);

echo "获取实时天气：\n";
$response = $w->getWeather('深圳');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

echo "\n\n获取天气预报：\n";
$response = $w->getWeather('深圳', 'all');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

echo "\n\n获取实时天气(XML)：\n";
$response = $w->getWeather('深圳', 'base', 'xml');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);