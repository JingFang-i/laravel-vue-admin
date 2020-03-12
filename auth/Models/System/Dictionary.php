<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    protected $table = 'dictionary';
    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
