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
        // return $request->input('api_token');
        return $request->user();
    });

    Route::post('/logout', 'AuthController@logout');
    Route::get('/message/history', 'MessageController@history');
    Route::get('/message/history/byUser', 'MessageController@byUser');

    Route::post('/file/uploadimg', 'FileController@uploadImage');
    Route::post('/file/avatar', 'FileController@avatar');

});

// 注册和登录
Route::post('/user/signup', 'AuthController@register');
Route::post('/user/signin', 'AuthController@login');

// Not Found
Route::fallback(function(){
    return response()->json(['message' => 'Resource not found.'], 404);
});

