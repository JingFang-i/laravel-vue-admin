<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'album_id',
        'name',
        'admin_id',
        'path',
        'mime_type',
        'size'
    ];
}
