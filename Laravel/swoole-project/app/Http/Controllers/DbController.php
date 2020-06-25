<?php

namespace App\Http\Controllers;

use App\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('db/index');
    }

    /**
     * 向“users”数据表中添加一条数据
     *
     * @return \Illuminate\Http\Response
     */
    public function addUser()
    {
        DB::table('users')->insert(
            ['name' => '超级赛亚人', 'email' => 'ppp@email', 'password' => '12345']
        );
        return response()->json([
            "code" => 0,
            "message" => "添加用户成功"
        ]);
    }

    /**
     * 向“users”数据表中添加多条数据
     *
     * @return \Illuminate\Http\Response
     */
    public function addMultiUser()
    {
        DB::table('users')->insert([
            ['name' => '赛亚人一号', 'email' => 'ppp1@email', 'password' => '12345'],
            ['name' => '赛亚人二号', 'email' => 'ppp2@email', 'password' => '12345']
        ]);
        return response()->json([
            "code" => 0,
            "message" => "添加多个用户成功"
        ]);
    }

    /**
     * 向“users”数据表中添加一条数据并获取自动递增的ID号
     *
     * @return \Illuminate\Http\Response
     */
    public function addUserGetId()
    {
        $id = DB::table('users')->insertGetId(
            ['name' => '赛亚人ID', 'email' => 'ppp-id@email', 'password' => '12345']
        );
        return response()->json([
            "code" => 0,
            "message" => "添加用户成功，用户ID：$id"
        ]);
    }

    /**
     * 删除 users 数据表中 id 大于10的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function delIdGtTen()
    {
        DB::table('users')->where('id', '>', 10)->delete();
        return response()->json([
            "code" => 0,
            "message" => "删除id大于10的用户成功"
        ]);
    }

    /**
     * 删除 users 数据表中 所有的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function delAll()
    {
        DB::table('users')->delete();
        return response()->json([
            "code" => 0,
            "message" => "删除users中所有的数据"
        ]);
    }

    /**
     * 清空 users 数据表
     *
     * @return \Illuminate\Http\Response
     */
    public function clear()
    {
        DB::table('users')->truncate();
        return response()->json([
            "code" => 0,
            "message" => "清空users表中的所有数据"
        ]);
    }

    /**
     * 更新 users 数据表中的一条数据
     *
     * @return \Illuminate\Http\Response
     */
    public function updateOne()
    {
        DB::table('users')->where('id', 2)->update(['password' => '111111']);
        return response()->json([
            "code" => 0,
            "message" => "更新 users 数据表中的一条数据"
        ]);
    }

    /**
     * 自增users数据表中的一个字段的值
     *
     * @return \Illuminate\Http\Response
     */
    public function increment()
    {
        DB::table('users')->increment('count');
        return response()->json([
            "code" => 0,
            "message" => "自增users数据表中的一个字段的值"
        ]);
    }

    /**
     * 自增10 users数据表中的一个字段的值
     *
     * @return \Illuminate\Http\Response
     */
    public function incrementTen()
    {
        DB::table('users')->increment('count', 10);
        return response()->json([
            "code" => 0,
            "message" => "自增10 users数据表中的一个字段的值"
        ]);
    }

    /**
     * 自减users数据表中的一个字段的值
     *
     * @return \Illuminate\Http\Response
     */
    public function decrement()
    {
        DB::table('users')->decrement('count');
        return response()->json([
            "code" => 0,
            "message" => "自减users数据表中的一个字段的值"
        ]);
    }

    /**
     * 自减10 users数据表中的一个字段的值
     *
     * @return \Illuminate\Http\Response
     */
    public function decrementTen()
    {
        DB::table('users')->decrement('count', 10);
        return response()->json([
            "code" => 0,
            "message" => "自减10 users数据表中的一个字段的值"
        ]);
    }

    /**
     * 获取 users 数据表中所有的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $users = DB::table('users')->get();
        return response()->json([
            "code" => 0,
            "message" => "获取 users 数据表中所有的数据",
            "data" => $users
        ]);
    }

    /**
     * 获取满足where条件的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function getWhere()
    {
        $users = DB::table('users')->where('password', '12345')->get();
        return response()->json([
            "code" => 0,
            "message" => "获取满足where条件的数据",
            "data" => $users
        ]);
    }

    /**
     * 获取满足whereOr条件的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrWhere()
    {
        DB::table('users')->where('password', '111111')->increment('count');
        $users = DB::table('users')
            ->where('password', '12345')
            ->orWhere('count', '>', 0)
            ->get();
        return response()->json([
            "code" => 0,
            "message" => "获取满足whereOr条件的数据",
            "data" => $users
        ]);
    }

    /**
     * 获取满足whereBetween条件的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function getWhereBetween()
    {
        $users = DB::table('users')
            ->whereBetween('id', [1, 3])
            ->get();
        return response()->json([
            "code" => 0,
            "message" => "获取满足whereBetween条件的数据",
            "data" => $users
        ]);
    }

    /**
     * 获取满足whereIn、whereNotIn条件的数据
     *
     * @return \Illuminate\Http\Response
     */
    public function getWhereIn()
    {
        $data = [
            "whereIn" => DB::table('users')->whereIn('id', [1, 2])->get(),
            "whereNotIn" => DB::table('users')->whereNotIn('id', [1, 3])->get(),
        ];
        return response()->json([
            "code" => 0,
            "message" => "获取满足whereIn、WhereNotInt条件的数据",
            "data" => $data
        ]);
    }
}
