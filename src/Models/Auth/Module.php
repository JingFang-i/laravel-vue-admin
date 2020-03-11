<?php

namespace Jmhc\Admin\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'weigh', 'status'];

    public $timestamps = false;
}
