<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>注册页面</title>
</head>
<body>
    <form action="/auth/register" method="post">
        {!! csrf_field() !!}
        <div>
            Name<input type="text" name="name" value="{{ old('name') }}" />
        </div>
        <div>
            Email<input type="email" name="email" value="{{ old('email') }}" />
        </div>
        <div>
            Password<input type="password" name="password" />
        </div>
        <div>
            Confirm Password<input type="password" name="password_confrimation" />
        </div>
        <div><button type="submit">Register</button></div>
    </form>
</body>
</html>
