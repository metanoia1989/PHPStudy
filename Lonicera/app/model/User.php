<?php
require_once _SYS_PATH.'core/Model.php';
/**
 * 用户表 x2_user 表的模型
 */
class User extends Model
{
    public $id;
    public $age;
    public $name;

    protected $rule = [
        'pk' => 'id',
        'pkStrategy' => 'generator',
    ];
}