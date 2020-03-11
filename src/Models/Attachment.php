<?php

namespace Jmhc\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'name',
        'admin_id',
        'path',
        'mime_type',
        'size'
    ];
}
