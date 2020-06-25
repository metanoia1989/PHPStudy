<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|string|max:255|unique:users',
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'bail|required|string|min:6',
            'src' => 'bail|active_url|max:255',
        ]);
        if ($validator->fails()) {
            return [
                'errno' => 1,
                'data' => $validator->errors()->first(),
            ];
        }

        // 在数据库中创建用户中并返回包含 api_token 字段的用户数据
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'avatar' => $request->input('src'),
                'password' => Hash::make($request->input('password')),
                'api_token' => Str::random(60),
            ]);
            if ($user) {
                return [
                    'errno' => 0,
                    'userInfo' => $user,
                    'data' => '注册成功',
                ];
            } else {
                return [
                    'errno' => 1,
                    'data' => '保存用户到数据库失败',
                ];
            }
        } catch (QueryException $exception) {
            return [
                'errno' => 1,
                'data' => '保存用户到数据库异常：'.$exception->getMessage(),
            ];
        }
    }

    public function login(Request $request)
    {
        // 验证登录字段
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return [
                'errno' => 1,
                'data' => $validator->errors()->first(),
            ];
        }

        $name = $request->input('name');
        $password = $request->input('password');
        $user = User::where('name', $name)->first();
        // 用户校验成功则返回Token信息
        if ($user && Hash::check($password, $user->password)) {
            $user->api_token = Str::random(60); // 登录成功更新 api_token
            $user->save();
            return ['userInfo' => $user, 'errno' => 0, 'data' => '登录成功'];
        }
        return ['errno' => 1, 'data' => '错误的用户名密码'];
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userModel = User::find($user->id);
        $userModel->api_token = null; // 注销登录，清除 api_token
        $userModel->save();
        return ['errno' => 0, 'data' => '注销登录成功！'];
    }
}

