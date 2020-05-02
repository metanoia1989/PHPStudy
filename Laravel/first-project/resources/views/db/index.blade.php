<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>查询构造器的使用</title>
</head>
<body>
    <h1>添加信息</h1>
    <div><a href="{{ url('db/addUser') }}">向“users”数据表中添加一条数据</a></div>
    <div><a href="{{ url('db/addMultiUser') }}">向“users”数据表中添加多条数据</a></div>
    <div><a href="{{ url('db/addUserGetId') }}">向“users”数据表中添加一条数据并获取自动递增的ID号</a></div>

    <h1>删除信息</h1>
    <div><a href="{{ url('db/delIdGtTen') }}">删除 users 数据表中 id 大于10的数据</a></div>
    <div><a href="{{ url('db/delAll') }}">删除 users 数据表中 所有的数据</a></div>
    <div><a href="{{ url('db/clear') }}">清空 users 数据表</a></div>

    <h1>修改数据</h1>
    <div><a href="{{ url('db/updateOne') }}">更新 users 数据表中的一条数据</a></div>
    <div><a href="{{ url('db/increment') }}">自增users数据表中的一个字段的值</a></div>
    <div><a href="{{ url('db/incrementTen') }}">自增10 users数据表中的一个字段的值</a></div>
    <div><a href="{{ url('db/decrement') }}">自减users数据表中的一个字段的值</a></div>
    <div><a href="{{ url('db/decrementTen') }}">自减 10 users数据表中的一个字段的值</a></div>

    <h1>查询数据</h1>
    <div><a href="{{ url('db/getAll') }}">获取 users 数据表中所有的数据</a></div>
    <div><a href="{{ url('db/getWhere') }}">获取满足where条件的数据</a></div>
    <div><a href="{{ url('db/getOrWhere') }}">获取满足orWhere条件的数据</a></div>
    <div><a href="{{ url('db/getWhereBetween') }}">获取满足whereBetween条件的数据</a></div>
    <div><a href="{{ url('db/getWhereIn') }}">获取满足whereIn条件的数据</a></div>
</body>
</html>
