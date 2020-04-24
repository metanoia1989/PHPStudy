<?php

class news_read
{
	public $G;

	public function __construct(&$G)
	{
		$this->G = $G;
	}

	public function _init()
	{
		$this->categories = NULL;
		$this->tidycategories = NULL;
		$this->sql = $this->G->make('sql');
		$this->pdosql = $this->G->make('pdosql');
		$this->db = $this->G->make('pepdo');
		$this->pg = $this->G->make('pg');
		$this->ev = $this->G->make('ev');
		$this->module = $this->G->make('module');
		$this->user = $this->G->make('user','user');
    }
   
    /** 
     * 获取内容列表
     */
    public function getNewsList($args,$page,$number = 20,
        $order = 'contentsequence DESC,contentinputtime DESC,contentid DESC')
    {
        $data = array(
            'select' => false,
            'table' => 'content',
            'query' => $args,
            'orderby' => $order
        );
        $rows = $this->db->listElements($page, $number, $data);
        return $rows;
    }

    /**
     * 添加一篇新闻
     *
     * @param array $args
     * @return void
     */
    public function addNews($args)
    {
       $data = [
            'table' => 'content',
            'query' => $args,
       ]; 
       return $this->db->insertElement($data);
    }

    /**
     * 删除一篇新闻
     *
     * @param integer $id
     * @return void
     */
    public function delNews($id)
    {
        $data = [
            'table' => 'content',
            'query' => [
                ['AND', 'contentid = :contentid', 'contentid', $id]
            ]
        ];
        return $this->db->delElement($data);
    }

    /**
     * 修改一篇新闻
     *
     * @param integer $id
     * @param array $args
     * @return void
     */
    public function modifyContent($id, $args)
    {
        if (isset($args['contentmoduleid']))
            unset($args['contentmoduleid']);
        $data = [
            'table' => 'content',
            'value' => $args,
            'query' => [
                ['AND', 'contentid = :contentid', 'contentid', $id]
            ]
        ];
        return $this->db->updateElement($data);
    }

    /**
     * 通过id获取对应的新闻
     *
     * @param integer $id
     * @return void
     */
    public function getNewsById($id)
    {
        $data = [ false, 'content', [[ 'AND', 'contentid = :contentid', 'contentid', $id ]] ];
        $sql = $this->pdosql->makeSelect($data);
        return $this->db->fetch($sql);
    }

    /**
     * 获取指定id新闻的上一页和下一页? 光看代码不太确定
     *
     * @param integer $id
     * @param integer $catid
     * @return array
     */
    public function getNearNewsById($id, $catid)
    {
        $rows = [];

        $data = [ false, 'content', [
            [ 'AND', 'contentid < :contentid', 'contentid', $id ],
            [ 'AND', 'contentcatid = :catid', 'catid', $catid ],
        ], false, 'contentid DESC', 5 ];
        $sql = $this->pdosql->makeSelect($data);
        $rows['pre'] = $this->db->fetchAll($sql);

        $data = [ false, 'content', [
            [ 'AND', 'contentid > :contentid', 'contentid', $id ],
            [ 'AND', 'contentcatid = :catid', 'catid', $catid ],
        ], false, 'contentid ASC', 5 ];
        $sql = $this->pdosql->makeSelect($data);
        $rows['next'] = $this->db->fetchAll($sql);

        return $rows;
    }
}