<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;
use Jmhc\Admin\Traits\SerializeDate;

class Dictionary extends Model
{
    use SerializeDate;

    protected $table = 'dictionary';
    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

}
