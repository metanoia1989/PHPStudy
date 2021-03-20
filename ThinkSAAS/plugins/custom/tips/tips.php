<?php

defined('IN_TS') or die('Access Denied.');

function tips() {
    $arrayTips = [
       '你可以绑定你的微博帐号，发帖的同时也发布微博哦',
       '你可以在帖子中上传多个附件',
       'ThinkSAAS支持灵活的标签(tag)分类功能',
    ];
    $i = mt_rand(0, count($arrayTips) - 1);
    $tip = $arrayTips[$i];
    echo $tip;
}

addAction('custom-hook', 'tips');
echo "hello";