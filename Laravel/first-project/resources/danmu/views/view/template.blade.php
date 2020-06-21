<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>模板控制语法</title>
</head>
<body>
    <h2>{{ $name }}</h2>
    <hr>
    <h2>{{ $user ?? 'default' }}</h2>
    <hr>
    <h2>@{{ 禁止解析 }}</h2>
    <hr>
    <h2>
        @if ($name == 'AdamSmith')
            i am {{ $name }}
        @else
            i am people.
        @endif
    </h2>
    <hr>
    @foreach ($users as $user)
        <h2>This user is {{ $user }}</h2>
    @endforeach
</body>
</html>
