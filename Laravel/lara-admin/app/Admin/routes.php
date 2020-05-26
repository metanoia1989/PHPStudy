<?php
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    // $router->get('/routes', function() {
    //     return collect()
    // });
    $router->get('users/layout', 'UserController@layout');
    $router->get('users/roles', 'UserController@roles');
    $router->get('users/profile', 'UserController@profile');
    $router->get('users/posts', 'UserController@posts');
    $router->resource('users', UserController::class);

    $router->get('movie', 'MovieController@index');

    $router->get('column/show', 'ShowController@index');
    $router->get('column/columnFilter', 'ShowController@columnFilter');
    $router->get('column/simple', 'ShowController@simple');
    $router->get('chartjs/index', 'ChartjsController@index');

});

