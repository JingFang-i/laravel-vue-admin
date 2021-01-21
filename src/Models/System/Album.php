<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jmhc\Admin\Traits\SerializeDate;

class Album extends Model
{
    use SoftDeletes, SerializeDate;

    protected $fillable = [
        'name',
        'cover_image',
        'weigh',
    ];

    public function getTable()
    {
        return config('admin.table_names.albums', parent::getTable());
    }
}
