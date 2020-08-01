<?php
return [
    'mode' => 'debug', // 应用程序模式，默认为调试模式
    'filter' => true, // 是否过滤 $_GET、$_POST、$_COOKIE、$_FILES
    'charSet' => 'utf-8', // 设置网页编码
    'defaultApp' => 'front', // 默认的分组
    'defaultController' => 'index', // 默认的控制器名称
    'defaultAction' => 'index', // 默认的动作名称
    'UrlControllerName' => 'c', // 自定义控制器名称，例如：index.php?c=index
    'UrlActionName' => 'a', // 自定义方法名称，例如：index.php?c=index&a=Index
    'UrlGroupName' => 'g', // 自定义分组名
    'db' => [

    ],
    'smtp' => [],
];