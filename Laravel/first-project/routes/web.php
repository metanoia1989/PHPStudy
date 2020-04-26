<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function() {
    return 'Hello Laravel';
});

Route::get('home', function() {
    return 'Hello Laravel';
});

Route::get('routes', function() {
    return collect(Route::getRoutes())->map(function ($route) {
        return $route->uri();
    });
});

// 请求方法
Route::match(['get', 'post'], 'method', function() {
    return "Hello Route Method";
});


// 路由参数
Route::get('user/{id}', function($id) {
    return '$id='.$id;
});

Route::get('user2/{name?}', function($name="Adam Smith") {
    return '$name='.$name;
});

Route::get('user3/{name}', function ($name) {
    return '$name = '.$name;
})->where('name', '[A-Za-z]+');

Route::get('user/{id}/{name}', function($id, $name) {
    return '$id = '.$id.' and '.' $name = '.$name;
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

// 路由命名
Route::get('user4/name', ['as' => 'name', 'uses' => function() {
    return route('name');
}]);

Route::get('user5/name', function() {
    return route('name-another');
})->name('name-another');

// 路由群组
// Route::group(['prefix' => 'user-group', 'middleware' => 'auth'], function(){
Route::group(['prefix' => 'user-group'], function(){
    Route::get('id', function() {
        return "用户组 个人中心";
    });
    Route::get('name', function() {
        return "用户组 名称修改";
    });
});


// 控制器路由
# 基础控制器路由
Route::get('home/{name}', 'HomeController@index');
Route::get('home/{id}/{name?}', 'HomeController@user');

# 隐式控制器路由 5.3版已弃用
// Route::controller('users', 'UserController');

# 资源控制器路由
Route::resource('resources', 'ResourceController');

# 模块/控制器/方法 module/controller/action 路由
Route::get('/{class}/{action}', function ($class, $action) {
    $instance = App ::make('App\\Http\\Controllers\\' . ucfirst($class) . 'Controller');
    return App::call([$instance, $action]);
});


// 视图
