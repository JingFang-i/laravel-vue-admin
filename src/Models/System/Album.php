<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cover_image',
        'weigh',
    ];

}
