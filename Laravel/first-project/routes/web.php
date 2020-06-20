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

// 投递异步任务的测试代码
Route::get('/task/test', function () {
    $task = new \App\Jobs\TestTask('测试异步任务'); // 创建任务实例
    $success = \Hhxsv5\LaravelS\Swoole\Task\Task::deliver($task); // 异步投递任务，触发调用任务类的 handle 方法
    Illuminate\Support\Facades\Log::info('任务结果：', [$success]);
    var_dump($success);
});

// 自定义事件测试
Route::get('/event/test', function () {
    $event = new \App\Events\TestEvent('测试异步事件监听及处理');
    $success = \Hhxsv5\LaravelS\Swoole\Task\Event::fire($event);
    var_dump($success);
    echo "hello";
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
Route::get('/{controller}/{action}', function ($controller, $action) {
    $instance = App ::make('App\\Http\\Controllers\\' . ucfirst($controller) . 'Controller');
    return App::call([$instance, $action]);
});


//*******************
//* 权限认证
//*******************
// // 认证路由
// Route::get('auth/login', 'Auth\AuthController@getLogin');
// Route::post('auth/login', 'Auth\AuthController@postLogin');
// Route::get('auth/logout', 'Auth\AuthController@getLogout');
// // 注册路由
// Route::get('auth/register', 'Auth\AuthController@getRegister');
// Route::post('auth/register', 'Auth\AuthController@postRegister');
// // 密码重置请求链接路由
// Route::get('password/email', 'Auth\PasswordController@getEmail');
// Route::post('password/email', 'Auth\PasswordController@postEmail');
// // 密码重置路由
// Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
// Route::post('password/reset', 'Auth\PasswordController@postReset');

