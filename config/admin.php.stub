<?php

return [
    'service_prefix'     => "App\\Services",
    'model_prefix'       => "App\\Models",
    'repository_prefix'  => "App\\Repositories",
    'controller_prefix'  => "App\\Http\\{:moduleName}\\Controllers",
    // 导入数据表 admin,areas admin为权限及系统设置数据表，areas为行政区域数据表
    'import_table'       => ['admin', 'areas'],
    'table_names'        => [
        // 管理员用户表
        'admin_users'  => 'admin_users',
        // 操作日志表
        'admin_log'    => 'admin_log',
        // 相册表
        'albums'       => 'albums',
        // 附件表
        'attachments'  => 'attachments',
        // 系统配置表
        'configs'      => 'configs',
        // 字典表
        'dictionary'   => 'dictionary',
    ],
    // 是否允许写入日志
    'allow_write_admin_log'  => env('ALLOW_WRITE_ADMIN_LOG', false),
    // 上传文件配置
    'upload' => [
        'ext'  => ['jpg', 'jpeg', 'png', 'gif', 'bpm', 'mp4', 'doc', 'dot', 'docx', 'xls', 'xlsx', 'pdf'],
        'size' => 10, //上传大小限制，单位M(兆)
        'path' => 'upload/' . date('Ymd'),
    ],
];
