<?php
// 开启session，前面依然不能有有输出
// 生成一个sessionID添加到响应首部，生成一个同名的文件存储在文件中
session_start();

// 使用 $_SESSION 数组中添加内容，PHP会将这个数组中的内容存储到文件中
$_SESSION['name'] = "Adam Smith";
$_SESSION['age'] = 20;