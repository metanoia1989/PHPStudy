<?php
// 开启会话
session_start();

// 删除数组中的数据
// 删除单个数据
unset($_SESSION['name']);

// 清空整个数组中的内容
$_SESSION = [];

// 删除客户端Cookie中的sessionID
setcookie(session_name(), "", time()-10, "/");

// 删除服务器中的session文件
session_destroy();

echo "删除成功！";