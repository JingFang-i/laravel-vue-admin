<?php

namespace Jmhc\Admin\Models\System;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'position',
        'type',
        'image',
        'url',
    ];

    protected $appends = ['position_text', 'type_text'];

    public function getPositionTextAttribute($value)
    {
        return app()->make('bannerService')->getPosition($this->position);
    }

    public function getTypeTextAttribute($type)
    {
        return app()->make('bannerService')->getType($this->type);
    }
}
