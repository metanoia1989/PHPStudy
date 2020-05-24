<?php

//********************************************************
// 哈希表实现 使用直接取余法
// Hash表实现步骤：
// 1. 创建一个固定大小的数组用于存放数据。
// 2. 设计 Hash 函数
// 3. 通过 Hash 函数把关键字映射到数组的某个位置，并在此位置上进行数据存取。
// 
// 解决Hash表冲突的常用方法：开放定址法和拉链法
// 拉链法：把相同 Hash 值的关键字节点以一个链表连接起来，
// 在查找元素时就必须遍历这条链表，比较链表中每个元素的关键字与查找
// 的关键字是否相等，如果相等就是要找的元素。   
// 
// 因为节点需要保存关键字 Key 和数据 Value，同时还要记录具有相同Hash值的节点，
// 需要创建一个HashNode类存储这些信息。
//********************************************************

class HashNode 
{
    /**
     * 节点的关键字
     *
     * @var string
     */
    public $key;

    /**
     * 节点的值
     *
     * @var mixed
     */
    public $value;

    /**
     * 指向具有相同Hash值节点的指针
     *
     * @var HashNode
     */
    public $nextNode;

    public function __construct($key, $value, $nextNode = NULL)
    {
        $this->key = $key; 
        $this->value = $value; 
        $this->nextNode = $nextNode; 
    }
}

class HashTable
{
    /**
     * 用于存储数据的数组
     *
     * @var array
     */
    private $buckets; 

    /**
     * 记录$buckets数组的大小
     *
     * @var integer
     */
    private $size = 10;

    public function __construct()
    {
        // SqlFixedArray是接近于C语言的数组，效率更高
        $this->buckets = new SplFixedArray($this->size);    
    }

    /**
     * hash函数，使用直接取余法，字符串则计算其ASCII码的和
     *
     * @param string $key
     * @return integer
     */
    private function hashfunc($key)
    {
        $strlen = strlen($key);
        $hashval = 0;
        for ($i = 0; $i < $strlen; $i++) {
            $hashval += ord($key[$i]);
        }
        return $hashval % $this->size;
    }

    /**
     * 插入数据，先通过Hash函数计算关键字所在Hash表的位置，然后保存数组到此位置
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function insert($key, $val)
    {
        $index = $this->hashfunc($key);
        /* 新创建一个节点 */
        // 如果此位置已被其他节点占用，把新节点的$nextNode指向此节点，否则把新节点的$nextNode设置为NULL
        if (isset($this->buckets[$index])) {
            $newNode = new HashNode($key, $val, $this->buckets[$index]);
        } else {
            $newNode = new HashNode($key, $val);
        }
        $this->buckets[$index] = $newNode;
    }

    /**
     * 查找数据，先通过Hash函数计算关键字所在Hash表的位置，然后返回此位置的数据
     *
     * @param string $key
     * @return mixed
     */
    public function find($key)
    {
        $index = $this->hashfunc($key);
        $current = $this->buckets[$index];
        while (isset($current)) { // 遍历当前链表
            if ($current->key == $key) { // 比较当前节点的关键字
                return $current->value;
            }
            $current = $current->nextNode; // 比较下一个节点
        }
        return NULL; // 查找失败
    }

    /**
     * 输出Hash表中的所有的数据
     *
     * @return void
     */
    public function dump()
    {
        return $this->buckets;
    }
}
