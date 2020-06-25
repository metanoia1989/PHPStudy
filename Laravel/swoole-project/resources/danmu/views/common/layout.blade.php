<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>思维笔记</title>
    <link rel="stylesheet" href="/backyard/css/bootstrap.min.css">
</head>
<body>
    @section('navbar')
        <p>This is the initial navbar.</p>
    @show
    @yield('content')
    <footer>
        <p>&copy; Company 2015</p>
    </footer>
    <script src="/backyard/js/jquery.min.js"></script>
</body>
</html>
