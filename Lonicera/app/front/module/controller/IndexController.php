<?php

class IndexController
{
    public function indexAction()
    {
        echo 'indexAction';
    }

    public function hiAction()
    {
        // 用原生SQL查询数据
        // require_once _SYS_PATH.'core/DB.php';
        // $db = DB::getInstance($GLOBALS['_config']['db']);
        // $ret = $db->query('select * from x2_user where usergroupid > :id', ['id' => 55]);
        // echo json_encode($ret);

        require_once _SYS_PATH.'core/Model.php';
        require_once _APP.'model/User.php';
        $user = new User();
        $user->name = 'baicai2';
        $user->age = 20;
        $ret = $user->save();
        var_dump($ret);
        var_dump($user);
    }

    public function updateAction()
    {

    }
}