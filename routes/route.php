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
        Route::resource('roles', 'Auth\RoleController');
        Route::post('roles/multi', 'Auth\RoleController@multi')->name('roles.multi');
        Route::post('roles/multi-del', 'Auth\RoleController@multiDestroy')->name('roles.multidestroy');
        //角色分配权限
        Route::post('assign-permission', 'Auth\RoleController@assignPermission')->name('roles.assign-permission');
        //权限
        Route::resource('permissions', 'Auth\PermissionController');
        Route::post('permissions/multi', 'Auth\PermissionController@multi')->name('permissions.multi');
        Route::post('permissions/multi-del', 'Auth\PermissionController@multiDestroy')->name('permissions.multidestroy');

        //获取配置组
        Route::get('config-group', 'System\ConfigController@getConfigGroup')->name('config-group');

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
        // 附件列表
        Route::get('attachments', 'System\AttachmentController@index');
        // 相册列表
        Route::get('albums', 'System\AlbumController@index');
        // 获取字典值
        Route::get('dict', 'System\DictionaryController@getDict');
    });

    // 需要验证权限且需要自动加载服务的路由
    Route::middleware(['auth:admin', 'service', 'permission:admin'])->group(function() {
        //管理员
        Route::resource('admin-users', 'Auth\AdminUserController');
        Route::post('admin-users/multi', 'Auth\AdminUserController@multi')->name('admin-users.multi');
        Route::post('admin-users/multi-del', 'Auth\AdminUserController@multiDestroy')->name('admin-users.multidestroy');
        //分配角色
        Route::post('assign-role', 'Auth\AdminUserController@assignRole')->name('admin-users.assign-role');

        // *** 系统设置 ***
        // 字典设置
        Route::resource('dictionary', 'System\DictionaryController');

        // 系统配置
        Route::resource('configs', 'System\ConfigController');
        Route::post('configs/update-group', 'System\ConfigController@updateGroup')->name('configs.update-group');

        // 相册详情
        Route::get('albums/{id}', 'System\AlbumController@show')->name('albums.show');
        // 更新相册
        Route::put('albums/{id}', 'System\AlbumController@update')->name('albums.update');
        // 新增相册
        Route::post('albums', 'System\AlbumController@store')->name('albums.store');
        // 删除相册
        Route::delete('albums/{id}', 'System\AlbumController@destroy')->name('albums.destroy');

        // 附件详情
        Route::get('attachments/{id}', 'System\AttachmentController@show')->name('attachments.show');
        // 更新附件
        Route::put('attachments/{id}', 'System\AttachmentController@update')->name('attachments.update');
        // 新增附件
        Route::post('attachments', 'System\AttachmentController@store')->name('attachments.store');
        // 删除附件
        Route::delete('attachments/{id}', 'System\AttachmentController@destroy')->name('attachments.destroy');
        // 批量更新
        Route::post('attachments/multi', 'System\AttachmentController@multi')->name('attachments.multi');
        // 批量删除
        Route::post('attachments/multi-del', 'System\AttachmentController@multiDestroy')->name('attachments.multidestroy');

        // AdminLog
        Route::resource('admin-log', 'System\AdminLogController');
        // AdminLog 批量操作
        Route::post('admin-log/multi', 'System\AdminLogController@multi')->name('admin-log.multi');
        // AdminLog 批量删除
        Route::post('admin-log/multi-del', 'System\AdminLogController@multiDestroy')->name('admin-log.multidestroy');

    });
});

