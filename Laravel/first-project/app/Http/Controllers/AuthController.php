<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

/**
 * 当用户注册成功或者登录成功，会更新 users 表的 api_token 字段值，当用户退出时，则清空该字段值
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }
    /**
     * 用户注册
     *
     * @param Request $request
     * @return User
     */
    public function register(Request $request)
    {
        // 验证注册字段
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ])->validate();

        // 在数据库中创建用户中并返回包含 api_token 字段的用户数据
        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'api_token' => Str::random(60),
        ]);
    }

    public function login(Request $request)
    {
        // 验证登录字段
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where('email', $email)->first();
        // 用户校验成功则返回Token信息
        if ($user && Hash::check($password, $user->password)) {
            return response()->json(['user' => $user, 'success' => true]);
        }
        return response()->json(['success' => false]);
    }
}

