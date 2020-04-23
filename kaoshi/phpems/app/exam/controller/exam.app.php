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
        if($this->data['currentbasic']['basicexam']['model'] != 2)
		{
            header("location:index.php?exam-app-lesson");
            exit;
		}
		$intime = 0;
        if($this->data['currentbasic']['basicexam']['opentime']['start'] || $this->data['currentbasic']['basicexam']['opentime']['end'])
		{
			if($this->data['currentbasic']['basicexam']['opentime']['start'] < TIME)
			{
				if($this->data['currentbasic']['basicexam']['opentime']['end'])
				{
					if($this->data['currentbasic']['basicexam']['opentime']['end'] > TIME)
					{
                        $intime = 1;
					}
				}
				else
				{
                    $intime = 1;
				}
			}
		}
		else
		{
            $intime = 1;
		}
        $this->tpl->assign('intime',$intime);
        if(!method_exists($this,$action))
		{
			$action = "index";
		}
		$this->$action();
		exit;
	}

	private function history()
	{
        $page = $this->ev->get('page');
        $ehtype = 2;
        $page = $page > 0?$page:1;
        $basicid = $this->data['currentbasic']['basicid'];
        $args = array(
            array("AND","ehuserid = :ehuserid",'ehuserid',$this->_user['sessionuserid']),
            array("AND","ehbasicid = :ehbasicid",'ehbasicid',$basicid),
            array("AND","ehtype = :ehtype",'ehtype',$ehtype)
        );
        $exams = $this->favor->getExamHistoryListByArgs($args,$page,10,false,'ehid desc');
		$this->tpl->assign('exams',$exams);
        $this->tpl->display('exam_history');
	}

    private function score()
    {
        if($this->data['currentbasic']['basicexam']['opentime']['end'])
		{
			if($this->data['currentbasic']['basicexam']['opentime']['end'] < TIME)
			{
                $message = array(
                    'statusCode' => 300,
                    "message" => "考场已关闭，不能交卷",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php?exam-app-exam"
                );
                $this->G->R($message);
			}
		}
    	$questype = $this->basic->getQuestypeList();
        if($this->ev->get('insertscore'))
        {
            $sessionvars = $this->exam->getExamSessionBySessionid();
            if($this->data['currentbasic']['basicexam']['examnumber'])
            {
                $overflow = false;
                $ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
                if(!$ids)$ids = '0';
                $number = array();
                if($ids)
                {
                    $ids = explode(',',$ids);
                    foreach($ids as $t)
                    {
                        $num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
                        $number['child'][$t] = $num;
                        $number['all'] = intval($number['all'])+$num;
                    }
                }
                if($this->data['currentbasic']['basicexam']['selectrule'])
                {
                    if($number['all'] >= $this->data['currentbasic']['basicexam']['examnumber'])
                    {
                        $overflow = true;
                    }
                }
//                else
//                {
//                    if($number['child'][$sessionvars['examsessionkey']] >= $this->data['currentbasic']['basicexam']['examnumber'])
//                    {
//                        $overflow = true;
//                    }
//                }
                if($overflow)
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "您的考试次数已经用完了！"
                    );
                    $this->G->R($message);
                }
            }
            if(!$sessionvars['examsessionid'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "非法参数",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php?exam-app-exam"
                );
                $this->G->R($message);
            }
            $question = $this->ev->get('question');
            $sessionvars['examsessionuseranswer'] = $question;
            $result = $this->exam->markscore($sessionvars,$questype);
            if($result['needhand'] && !$sessionvars['examsessionsetting']['examdecide'])
            {
                $message = array(
                    'statusCode' => 200,
                    "message" => "交卷成功",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php?exam-app-history-makescore&ehid={$result['ehid']}"
                );
            }
            else
            {
                $message = array(
                    'statusCode' => 200,
                    "message" => "交卷成功",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php?exam-app-history-stats&ehid={$result['ehid']}"
                );
            }
            $this->G->R($message);
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "非法参数"
            );
            $this->G->R($message);
        }
    }

	private function paper()
	{
		$sessionvars = $this->exam->getExamSessionBySessionid();
        if(!$sessionvars['examsessionid'])
        {
            header("location:index.php?exam-app-exam");
            exit;
        }
        $lefttime = 0;
        $questype = $this->basic->getQuestypeList();
		$this->tpl->assign('questype',$questype);
		$this->tpl->assign('sessionvars',$sessionvars);
		$this->tpl->assign('lefttime',$lefttime);
		$this->tpl->assign('donumber',is_array($sessionvars['examsessionuseranswer'])?count($sessionvars['examsessionuseranswer']):0);
		if($this->data['currentbasic']['basicexam']['selftemplate'])
		$this->tpl->display($this->data['currentbasic']['basicexam']['selftemplate']);
		else
		$this->tpl->display('exam_paper');
	}

	private function selectquestions()
	{
		$this->exam->delExamSession();
		if($this->data['currentbasic']['basicexam']['selectrule'])
		{
			$ids = explode(',',trim($this->data['currentbasic']['basicexam']['self'],', '));
			$p = rand(0,count($ids)-1);
			$examid = $ids[$p];
		}
		else
		$examid = $this->ev->get('examid');
		$r = $this->exam->getExamSettingById($examid);
		if(!$r['examid'])
		{
			$message = array(
				'statusCode' => 300,
				"message" => "参数错误，尝试退出后重新进入"
			);
			$this->G->R($message);
		}
		else
		{
            if($this->data['currentbasic']['basicexam']['examnumber'])
            {
                $overflow = false;
                $ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
                if(!$ids)$ids = '0';
                $number = array();
                if($ids)
                {
                    $ids = explode(',',$ids);
                    foreach($ids as $t)
                    {
                        $num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
                        $number['child'][$t] = $num;
                        $number['all'] = intval($number['all'])+$num;
                    }
                }
                if($this->data['currentbasic']['basicexam']['selectrule'])
                {
                    if($number['all'] >= $this->data['currentbasic']['basicexam']['examnumber'])
                    {
                        $overflow = true;
                    }
                }
//                else
//                {
//                    if($number['child'][$r['examid']] >= $this->data['currentbasic']['basicexam']['examnumber'])
//                    {
//                        $overflow = true;
//                    }
//                }
                if($overflow)
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "您的考试次数已经用完了！"
                    );
                    $this->G->R($message);
                }
            }
			if($r['examtype'] == 1)
			{
				$questionids = $this->question->selectQuestions($examid,$this->data['currentbasic']);
				$questions = array();
				$questionrows = array();
				foreach($questionids['question'] as $key => $p)
				{
                    $questions[$key] = $this->exam->getQuestionListByIds($p);
				}
				foreach($questionids['questionrow'] as $key => $p)
				{
					$ids = "";
					if(is_array($p))
					{
						if(count($p))
						{
							foreach($p as $t)
							{
								$questionrows[$key][$t] = $this->exam->getQuestionRowsById($t);
							}
						}
					}
					else $questionrows[$key][$p] = $this->exam->getQuestionRowsById($p);
				}
				$sargs['examsessionquestion'] = array('questionids'=>$questionids,'questions'=>$questions,'questionrows'=>$questionrows);
				$sargs['examsessionsetting'] = $questionids['setting'];
				$sargs['examsessionstarttime'] = TIME;
				$sargs['examsession'] = $questionids['setting']['exam'];
				$sargs['examsessiontime'] = $questionids['setting']['examsetting']['examtime']>0?$questionids['setting']['examsetting']['examtime']:60;
				$sargs['examsessionstatus'] = 0;
				$sargs['examsessiontype'] = 2;
				$sargs['examsessionsign'] = '';
				$sargs['examsessionuseranswer'] = '';
				$sargs['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$sargs['examsessionkey'] = $examid;
				$sargs['examsessionissave'] = 0;
				$sargs['examsessionsign'] = '';
				$sargs['examsessionuserid'] = $this->_user['sessionuserid'];
				$this->exam->insertExamSession($sargs);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
			elseif($r['examtype'] == 2)
			{
				$questions = array();
				$questionrows = array();
				foreach($r['examquestions'] as $key => $p)
				{
					$qids = '';
					$qrids = '';
					if($p['questions'])$qids = trim($p['questions']," ,");
					if($qids)
					$questions[$key] = $this->exam->getQuestionListByIds($qids);
					if($p['rowsquestions'])$qrids = trim($p['rowsquestions']," ,");
					if($qrids)
					{
						$qrids = explode(",",$qrids);
						foreach($qrids as $t)
						{
							$qr = $this->exam->getQuestionRowsById($t);
							if($qr)
							$questionrows[$key][$t] = $qr;
						}
					}
				}
				$args['examsessionquestion'] = array('questions'=>$questions,'questionrows'=>$questionrows);
				$args['examsessionsetting'] = $r;
				$args['examsessionstarttime'] = TIME;
				$args['examsession'] = $r['exam'];
				$args['examsessionscore'] = 0;
				$args['examsessionuseranswer'] = '';
				$args['examsessionscorelist'] = '';
				$args['examsessionsign'] = '';
				$args['examsessiontime'] = $r['examsetting']['examtime'];
				$args['examsessionstatus'] = 0;
				$args['examsessiontype'] = 2;
				$args['examsessionkey'] = $r['examid'];
				$args['examsessionissave'] = 0;
				$args['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$args['examsessionuserid'] = $this->_user['sessionuserid'];
				$this->exam->insertExamSession($args);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
			else
			{
				$args['examsessionquestion'] = $r['examquestions'];
				$args['examsessionsetting'] = $r;
				$args['examsessionstarttime'] = TIME;
				$args['examsession'] = $r['exam'];
				$args['examsessionscore'] = 0;
				$args['examsessionuseranswer'] = '';
				$args['examsessionscorelist'] = '';
				$args['examsessionsign'] = '';
				$args['examsessiontime'] = $r['examsetting']['examtime'];
				$args['examsessionstatus'] = 0;
				$args['examsessiontype'] = 2;
				$args['examsessionkey'] = $r['examid'];
				$args['examsessionissave'] = 0;
				$args['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$args['examsessionuserid'] = $this->_user['sessionuserid'];
				$this->exam->insertExamSession($args);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
		}
	}

	private function index()
	{
		$page = $this->ev->get('page');
		$ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
		if(!$ids)$ids = '0';
		$exams = $this->exam->getExamSettingList(array(array("AND","find_in_set(examid,:examid)",'examid',$ids)),$page,20);
        $sessionvars = $this->exam->getExamSessionByUserid($this->_user['sessionuserid'],$this->data['currentbasic']['basicid']);
        if($sessionvars && ($sessionvars['examsessionbasic'] == $this->_user['sessioncurrent']) && ($sessionvars['examsessionstatus'] < 2) && ($sessionvars['examsessiontype'] == 2))
        {
        	$this->tpl->assign('sessionvars',$sessionvars);
        }
        $number = array();
        if($ids)
        {
            $ids = explode(',',$ids);
            foreach($ids as $t)
            {
                $num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
                $number['child'][$t] = $num;
                $number['all'] = intval($number['all'])+$num;
            }
        }
        $this->tpl->assign('number',$number);
        $this->tpl->assign('exams',$exams);
		$this->tpl->display('exam');
	}
}


?>
