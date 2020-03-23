<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    protected $table = 'dictionary';
    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

}
