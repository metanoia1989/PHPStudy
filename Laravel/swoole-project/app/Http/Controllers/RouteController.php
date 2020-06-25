<?php
namespace App\Http\Controllers;

class RouteController extends Controller
{

    public function implicit()
    {
        return "这是隐式路由 module/controller/action";
    }
}
