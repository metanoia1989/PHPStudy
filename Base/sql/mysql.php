<?php

// 连接数据库
$link = mysqli_connect("localhost", "root", "root") or die("Could not connect: {mysql_error()}");
// 选择数据库
mysqli_select_db($link, "lara_first");

// 执行插入语句
$sql1 = "insert into users(name, email, password) values('xiaofeng', 'e234@email','123456')";
mysqli_query($link, $sql1);
echo "<h3>插入的ID号是：".mysqli_insert_id($link)."</h3>";
echo  "<h3>影响记录的行数：".mysqli_affected_rows($link)."</h3>";
$sql2 = "insert into users(name, email, password) values('shuoshuo', 'e235@email', '123456')";
mysqli_query($link, $sql2);
echo "<h3>插入的ID号是：".mysqli_insert_id($link)."</h3>";
echo  "<h3>影响记录的行数：".mysqli_affected_rows($link)."</h3>";

// 执行插入语句
$sql4 = "update users set name='superadmin' where id=3";
mysqli_query($link, $sql4);
echo  "<h3>更新影响记录的行数：".mysqli_affected_rows($link)."</h3>";

// 关闭数据库
mysqli_close($link);