<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;
use Jmhc\Admin\Services\System\AdminLogService;

class AdminLog extends Model
{
    protected $table = 'admin_log';

    protected $fillable = [
        'admin_id',
        'name',
        'title',
        'ip',
        'content',
    ];

    protected $appends = [];

    protected $casts = [
        'content' => 'json',
    ];
}
