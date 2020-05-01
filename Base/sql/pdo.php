<?php
// 连接数据库
$dsn = "mysql:host=localhost;dbname=lara_first";
try {
    $pdo = new PDO($dsn, "root", "root");
} catch (PDOException $e) {
    echo "<h3>连接失败".$e->getMessage()."</h3>";
}

// 使用PDO类中的exec()方法插入数据
$sql1 = "insert into users(name, email, password) values ('AdamSmith', 'ada@email', '123456')";
$row = $pdo->exec($sql1);
echo "<h3>插入语句影响的行号为：".$pdo->lastInsertId()."</h3>";

// 使用 PDOStatement 类中的 execute() 方法插入数据
$sql2 = "insert into users(name, email, password) values (:name, :email, :password)";
$state = $pdo->prepare($sql2);
$state->execute(["name" => "原人亚当", "email" => "mwe@email", "password" => "123456"]);
echo "<h3>插入语句影响的行号为：".$pdo->lastInsertId()."</h3>";

// 通过 PDOStatement 的 execute() 方法执行查询语句
$sql5 = "select * from users where id > :id";
$pdoSta = $pdo->prepare($sql5);
$pdoSta->execute(["id" => 0]);
while ($row = $pdoSta->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
    echo "<br>";
}