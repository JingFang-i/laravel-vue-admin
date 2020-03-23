<?php

namespace Jmhc\Admin\Repositories\System;

use Jmhc\Admin\Repository;

class AdminLogRepository extends Repository
{
    // 允许查出的字段
    protected $allowFields = [
        'id',
        'admin_id',
        'name',
        'title',
        'ip',
        'content',
        'created_at',
        'updated_at',
    ];

    // 排序字段
    protected $orderField  = 'created_at';
}
