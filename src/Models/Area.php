<?php

namespace Jmhc\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'id',
        'pid',
        'shortname',
        'name',
        'mergename',
        'level',
        'pinyin',
        'code',
        'zip',
        'first',
        'lng',
        'lat',
    ];

    protected $append = [];


}
