<?php

define('_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR); // 网站根目录
define('_SYS_PATH', _ROOT.'Lonicera'.DIRECTORY_SEPARATOR); // 系统目录
define('_APP', _ROOT.'app'.DIRECTORY_SEPARATOR); // 应用根目录
require _SYS_PATH.'Lonicera.php'; // 框架boostrap
require _SYS_PATH.'config.php'; // 配置文件

$app = new Lonicera;
$app->run();