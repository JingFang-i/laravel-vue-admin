<?php

namespace App\Models;

use App\Services\ConfigService;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'id',
        'title',
        'group',
        'name',
        'type',
        'value',
        'options',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'type_text',
    ];

    protected $casts = [
        'options' => 'json',
    ];

    public function getTypeTextAttribute($value)
    {
        return ConfigService::instance()->getType($value);
    }

}
