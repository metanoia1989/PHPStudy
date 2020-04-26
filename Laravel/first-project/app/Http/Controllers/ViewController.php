<?php
namespace App\Http\Controllers;

class ViewController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function list()
    {
        return view('view/list');
    }

    public function params()
    {
        return view('view/params', ['username' => 'AdamSmith', 'age' => 34]);
        // return view('view/params')->with('username', 'AdamSmith')->with('age', 24);
        // return view('view/params')->withUsername('AdamSmith')->withAge(24);
    }

    /**
     * 模板继承
     */
    public function view()
    {
        return view('view/view');
    }

    /**
     * 模板的控制语法
     */
    public function template()
    {
        $data = [
            'name' => 'AdamSmith',
            'users' => [
                '原人亚当', '猿人凯撒', '现代人斯密斯'
            ]
        ];
        return view('view/template', $data);
    }
}
