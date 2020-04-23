<?php
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class action extends app
{
	public function display()
	{
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}


	private function index()
	{
        $this->course = $this->G->make('course','course');
        $this->content = $this->G->make('content','content');
        $this->position = $this->G->make('position','content');
        $courses = $this->course->getCourseList(array(),1,10);
        $basic = $this->G->make('basic','exam');
        $basics = $basic->getBasicList(array(),1,10);
        $topimgs = $this->position->getPosNewsList(array(array("AND","pcposid = 4")),1,5);
        $topnews = $this->position->getPosNewsList(array(array("AND","pcposid = 2")),1,10);
        $links = $this->content->getContentList(array(array("AND","contentcatid = 11")),1,10);
        $notices = $this->content->getContentList(array(array("AND","contentcatid = 26")),1,10);
        $this->tpl->assign('notices',$notices);
        $this->tpl->assign('links',$links);
        $this->tpl->assign('courses',$courses);
        $this->tpl->assign('basics',$basics);
        $this->tpl->assign('topimgs',$topimgs);
        $this->tpl->assign('topnews',$topnews);
		$this->tpl->display('index');
	}
}


?>
