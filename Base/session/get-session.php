<?php
// 判断客户端是否发送sessionID，如果发送，则不再向客户端发送sessionID
// 查找sessionID同名的文件，并将存储的数据读取到 $_SESSION数组中
session_start();
var_dump($_SESSION);