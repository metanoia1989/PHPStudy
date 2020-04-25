<?php 
/**
 * 测试数据库连接
 */

define("PE_VERSION",'6.0');
define("PEPATH",dirname(dirname(__FILE__)));

if (file_exists(PEPATH."/debug.php"))
    require PEPATH."/debug.php";

require PEPATH.'/lib/config.inc.php';
require PEPATH."/lib/init.cls.php";

$ginkgo = new ginkgo;
$dbClient = $ginkgo->make('pepdo');
$sql = $ginkgo->make('pdosql'); // sql生成器
/**
 * db数据库类的方法测试
 * connect(...) 连接数据库，query()方法里已封装连接。
 * exec($sql) 执行非查询SQL语句
 * query($sql) 执行查询SQL语句
 * fetch(...) 查询之后，获取单条记录
 * fetchAll(...) 查询之后，获取多条记录
 * affectedRows() 返回受影响的记录数 
 * lastInsertId() 返回插入的记录的ID
 * insertElement($args) 插入记录
 * delElement($args) 删除记录
 * updateElement($args) 更新记录
 * listElements($page,$number = 20,$args,$type = 1) 列出数据
 * beginTransaction、commit、rollback 事务操作
 */

# exec 执行sql语句
$dbSql = "CREATE TABLE IF NOT EXISTS `x2_test` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` varchar(255),
        `status` tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
$dbClient->query($dbSql);

# insertElement 插入记录
$dbArgs = [
    'table' => 'test',
    'query' => [
        'name' => '原人亚当',
        'description' => '第一个人，寂静圆满'
    ],
];
$insertId = $dbClient->insertElement($dbArgs);
if ($insertId) {
    echo "插入一条数据，id：{$insertId}<br>";
} else {
    echo '插入数据失败，请查看错误日志：'.PEPATH.'/data/error.log';
}

# updateElement 更新记录
$dbArgs = [
    'table' => 'test',
    'value' => ['status' => 0],
    'query' => [
        ['AND', 'id = :id', 'id', $insertId],
    ],
];
$updateRows = $dbClient->updateElement($dbArgs);
echo "更新了{$updateRows}条数据.<br>";

# query, fetch 查询数据
$selectArgs = [
    false, // 要查询的字段，及字段别名
    'test', // 表
    [[ 'AND', 'id > :id', 'id', 0 ]], // 条件
    false, // GROUP BY
    false, // ORDER BY
    1, // LIMIT
];
  	
$sqlArgs = $sql->makeSelect($selectArgs);
$row = $dbClient->fetch($sqlArgs);
echo "查询数据表 test 结果为：<br>";
echo "<code>";
echo json_encode($row);
echo "</code><br>";

# delElement 删除记录
$dbArgs = [
    'table' => 'test',
    'query' => [
        ['AND', 'id <= :id', 'id', $insertId]
    ],
];
$deleteRows = $dbClient->delElement($dbArgs);
echo "删除了{$deleteRows}条数据 id < {$insertId}.<br>";

# listElements 列出数据
$dbArgs = [
    'table' => 'test',
    'query' => [
        'name' => '超级赛亚号',
        'description' => '高达欧耶'
    ],
];
$dbClient->insertElement($dbArgs);
$dbArgs = [
    'table' => 'test',
    'query' => [
        'name' => '呼和浩特',
        'description' => '一座城市'
    ],
];
$dbClient->insertElement($dbArgs);

$dbArgs = [
    'select' => false,
    'table' => 'test',
    'query' => [['AND', 'id > :id', 'id', 0]],
    'groupby' => false,
    'orderby' => 'id DESC',
    // 'serial' => '', 需要反序列化的字段
];
$rows = $dbClient->listElements(1, 10, $dbArgs);
echo "listElements查询 结果为：<br>";
echo "<code>";
var_dump($rows);
echo "</code>";