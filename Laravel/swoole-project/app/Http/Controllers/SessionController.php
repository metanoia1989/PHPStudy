<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $session = $request->session();
        return $session->all();
    }

    public function put()
    {
        Session::put('name', 'Adam Smith');
        return response()->json([
            "code" => 0,
            "message" => "设置name到session中"
        ]);
    }

    public function get()
    {
        return Session::get('name');
    }

    public function helper()
    {
        $session = session()->driver();
        return $session->all();
    }

    public function push()
    {
        Session::push('contact.phone', '135938871052');
        return Session::all();
    }

    public function read()
    {
        return [
            'get_default' => Session::get('key', 'default'),
            'pull' => Session::pull('contact')
        ];
    }

    public function delName()
    {
        Session::forget('name');
        return Session::all();
    }

    public function clear()
    {
        Session::flush();
        return Session::all();
    }

    public function flash()
    {
        Session::flash('key', 'value');
        return Session::all();
    }

    public function reflash()
    {
        Session::reflash('key');
        return Session::all();
    }

    public function keep()
    {
        Session::keep(['key']);
        return Session::all();
    }

}
