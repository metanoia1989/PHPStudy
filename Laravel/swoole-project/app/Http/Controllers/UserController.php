<?php
namespace App\Http\Controllers;

class UserController extends Controller
{
    public function getIndex($username)
    {
        return "Hello $username";
    }

    public function getProfile($id)
    {
        return "<h1>用户 $id 的信息</h1>";
    }

}
