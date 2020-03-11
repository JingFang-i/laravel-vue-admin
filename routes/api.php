<?php

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
Route::prefix('v1')->namespace('v1')->group(function () {

    Route::middleware('auth:api')->group(function() {
        //上传
        Route::post('upload', 'CommonController@upload');
    });

    Route::middleware(['auth:api', 'service', 'status'])->group(function() {
        //用户信息
        Route::get('user', 'User\UserController@user');
        //配置
        Route::get('config', 'ConfigController@index');
    });
});

