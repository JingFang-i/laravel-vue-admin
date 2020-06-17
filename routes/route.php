<?php


Route::prefix('admin')->namespace('Jmhc\Admin\Controllers')->middleware('api')->group(function () {
    // 用户认证
    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');

    Route::get('test', 'TestController@index')->middleware('service');
    Route::match(['get', 'post'], 'ueditor', 'CommonController@ueditor');

    // 需要验证权限的路由
    Route::middleware(['auth:admin', 'permission:admin'])->group(function () {

        // 更新个人信息
        Route::post('update-info', 'Auth\AuthController@updateInfo')->name('admin-users.update-self');
        //角色
        Route::apiResource('roles', 'Auth\RoleController');
        Route::post('roles/multi', 'Auth\RoleController@multi')->name('roles.multi');
        Route::post('roles/multi-del', 'Auth\RoleController@multiDestroy')->name('roles.multidestroy');
        //角色分配权限
        Route::post('assign-permission', 'Auth\RoleController@assignPermission')->name('roles.assign-permission');
        //权限
        Route::apiResource('permissions', 'Auth\PermissionController');
        Route::post('permissions/multi', 'Auth\PermissionController@multi')->name('permissions.multi');
        Route::post('permissions/multi-del', 'Auth\PermissionController@multiDestroy')->name('permissions.multidestroy');

        //获取配置组
        Route::get('config-group', 'System\ConfigController@getConfigGroup')->name('admin-users.config-group');

    });

    // 不需要验证权限的路由
    Route::middleware(['auth:admin', 'service'])->group(function() {
        // 获取用户信息
        Route::get('user', 'Auth\AuthController@user');
        //上传
        Route::post('upload', 'CommonController@upload');
        //获取菜单和权限
        Route::get('auth', 'Auth\PermissionController@auth');
        //获取站点配置
        Route::get('website-config', 'System\ConfigController@getWebsiteConfig');
    });

    // 需要验证权限且需要自动加载服务的路由
    Route::middleware(['auth:admin', 'service', 'permission:admin'])->group(function() {
        //管理员
        Route::apiResource('admin-users', 'Auth\AdminUserController');
        Route::post('admin-users/multi', 'Auth\AdminUserController@multi')->name('admin-users.multi');
        Route::post('admin-users/multi-del', 'Auth\AdminUserController@multiDestroy')->name('admin-users.multidestroy');
        //分配角色
        Route::post('assign-role', 'Auth\AdminUserController@assignRole')->name('admin-users.assign-role');

        // *** 系统设置 ***
        // 字典设置
        Route::apiResource('dictionary', 'System\DictionaryController');
        // 系统配置
        Route::apiResource('configs', 'System\ConfigController');
        Route::post('configs/update-group', 'System\ConfigController@updateGroup')->name('configs.update-group');

        // 相册
        Route::apiResource('albums', 'System\AlbumController');
        // 图片管理
        Route::apiResource('attachments', 'System\AttachmentController');
        Route::post('attachments/multi', 'System\AttachmentController@multi')->name('attachments.multi');
        Route::post('attachments/multi-del', 'System\AttachmentController@multiDestroy')->name('attachments.multidestroy');

        // AdminLog
        Route::apiResource('admin-log', 'System\AdminLogController');
        // AdminLog 批量操作
        Route::post('admin-log/multi', 'System\AdminLogController@multi')->name('admin-log.multi');
        // AdminLog 批量删除
        Route::post('admin-log/multi-del', 'System\AdminLogController@multiDestroy')->name('admin-log.multidestroy');

    });
});

