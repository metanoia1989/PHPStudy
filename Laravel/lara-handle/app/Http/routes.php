<?php 
$app['router']->get('/', function() {
    return '<h1>路由成功！！！</h1>';
});

$app['router']->get('welcome', 
    'App\Http\Controllers\WelcomeController@index');