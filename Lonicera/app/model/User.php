<?php
namespace app\model;

use Lonicera\core\Model;

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