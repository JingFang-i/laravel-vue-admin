<?php

namespace Jmhc\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'test';

    protected $fillable = [
        'id',
        'name',
    ];

    protected $appends = [];


}
