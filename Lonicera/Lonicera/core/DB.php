<?php
namespace Lonicera\core;

/**
 * 数据库操作类，包括基本的connect和CURD
 */
class DB
{
    /**
     * 当前类的示例
     *
     * @var self
     */
    private static $instance;

    /**
     * 数据库连接
     *
     * @var PDO
     */
    private $dbLink;

    protected $queryNum = 0;

    protected $PDOStatement;

    protected $transTimes = 0; // 事务数

    protected $bind = [];

    public $rows = 0;

    private function __construct($config)
    {
        $this->connect($config);
    }

    public static function getInstance($config)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function connect($config)
    {
        try {
            $this->dbLink = new \PDO($config['dsn'], $config['username'], $config['password'], $config['param']);
        } catch (\PDOException $e) {
            throw $e; // 此异常无法处理，记录日志后往上层抛出
        }
    }

    public function query($sql, $bind = [], $fetchType = \PDO::FETCH_ASSOC)
    {
        if (!$this->dbLink) {
            throw new \Exception("数据库连接失败");
        }
        $this->PDOStatement = $this->dbLink->prepare($sql);
        $this->PDOStatement->execute($bind);
        $ret = $this->PDOStatement->fetchAll($fetchType);
        $this->rows = count($ret);
        return $ret;
    }

    public function execute($sql, $bind = [])
    {
        if (!$this->dbLink) {
            throw new \Exception("数据库连接失败");
        }
        $this->PDOStatement = $this->dbLink->prepare($sql);
        $ret = $this->PDOStatement->execute($bind);
        $this->rows = $this->PDOStatement->rowCount();
        return $ret;
    }

    /**
     * 开始事务
     *
     * @return void
     */
    public function startTrans()
    {
        ++$this->transTimes;
        if ($this->transTimes === 1) {
            $this->dbLink->beginTransaction(); // 不存在已创建事务才开启新的事务
        } else {
            $this->dbLink->exec("SAVEPOINT tr{$this->transTimes}"); // 创建一个 svaepoint
        }
    }

    public function commit()
    {
        if ($this->transTimes === 1) {
            $this->dbLink->commit();
        }
        --$this->transTimes;
    }

    public function rollback()
    {
        if ($this->transTimes === 1) {
            $this->dbLink->rollback();
        } elseif ($this->transTimes > 1) {
            $this->dbLink->exec("ROLLBACK TO SAVEPOINT tr{$this->transTimes}");
        }
        $this->transTimes = max(0, $this->transTimes - 1);
    }
}