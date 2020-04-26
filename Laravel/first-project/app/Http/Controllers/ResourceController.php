<?php
namespace App\Http\Controllers;

class ResourceController extends Controller
{
    public function index()
    {
        return 'index - 索引 GET';
    }

    public function create()
    {
        return 'create - 创建 GET';
    }

    public function store()
    {
        return 'store - 保存 POST';
    }

    public function show($id)
    {
        return "show $id - 显示 GET";
    }

    public function edit($id)
    {
        return "edit $id - 编辑 GET";
    }

    public function update($id)
    {
        return "update $id - 更新 PUT/PATCH";
    }

    public function destroy($id)
    {
        return "destroy $id - 删除 DELETE";
    }
}
