<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index($username)
    {
        return "Hello $username";
    }

    public function user($name, $id = null)
    {
        return "Hello, $name, $id";
    }

    public function implicit()
    {
        return "这是隐式路由 module/controller/action";
    }
}
