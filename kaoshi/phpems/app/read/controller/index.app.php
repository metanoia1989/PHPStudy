<?php

class action extends app
{
    public function display()
    {
        if ($this->ev->isMobile()) {
            header("location:index.php?read-phone");
            exit;
        }
        $action = $this->ev->url(3);
        if (!method_exists($this, $action)) {
            $action = "index";
        }

        $this->$action();
        exit;
    }

    private function index()
    {
        // $news = $this->news->getNewsList();
        // var_dump($news);
        echo "哈哈哈哈";
        // $this->tpl->assign('news_data', $news); 
        // $this->tpl->display('index');
    }

    public function test()
    {
        echo "测试";
    }
}
