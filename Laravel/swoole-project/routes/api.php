<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', 'AuthController@logout');
    Route::get('/history/message', 'MessageController@history');

    Route::post('/file/uploadimg', 'FileController@uploadImage');
    Route::post('/file/avatar', 'FileController@avatar');

    Route::get('/socket.io', 'SocketIOController@upgrade');
    Route::post('/socket.io', 'SocketIOController@ok');
});

// 注册和登录
Route::post('/user/signup', 'AuthController@register');
Route::post('/user/signin', 'AuthController@login');

// Not Found
Route::fallback(function(){
    return response()->json(['message' => 'Resource not found.'], 404);
});
