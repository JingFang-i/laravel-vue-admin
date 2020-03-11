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
    // 用户认证
    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');

    Route::get('test', 'TestController@index')->middleware('service');

    /**
     * 不需要验证权限的路由
     */
    Route::middleware(['auth:admin'])->group(function () {
        // 获取用户信息
        Route::get('user', 'Auth\AuthController@user');
        //上传
        Route::post('upload', 'CommonController@upload');
        //获取菜单和权限
        Route::get('auth', 'Auth\PermissionController@auth');
        //角色
        Route::resource('roles', 'Auth\RoleController');
        Route::post('roles/multi', 'Auth\RoleController@multi')->name('roles.multi');
        Route::post('roles/multi-del', 'Auth\RoleController@multiDestroy')->name('roles.multidestroy');
        //角色分配权限
        Route::post('assign-permission', 'Auth\RoleController@assignPermission')->name('assign-permission');
        //权限
        Route::resource('permissions', 'Auth\PermissionController');
        Route::post('permissions/multi', 'Auth\PermissionController@multi')->name('permissions.multi');
        Route::post('permissions/multi-del', 'Auth\PermissionController@multiDestroy')->name('permissions.multidestroy');

        //管理员
        Route::resource('users', 'Auth\AdminController');
        Route::post('users/multi', 'Auth\AdminController@multi')->name('users.multi');
        Route::post('users/multi-del', 'Auth\AdminController@multiDestroy')->name('users.multidestroy');
        //分配角色
        Route::post('assign-role', 'Auth\AdminController@assignRole');
    });

    Route::middleware(['auth:admin', 'service'])->group(function() {

        // *** 系统设置 ***
        // 字典设置
        Route::resource('dictionary', 'System\DictionaryController');
        //商城设置
        Route::resource('configs', 'System\ConfigController');
        Route::post('configs/update-group', 'System\ConfigController@updateGroup');
        // 轮播图
        Route::resource('banners', 'System\BannerController');
    });
});


