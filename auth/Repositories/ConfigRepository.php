<?php


namespace App\Repositories;

use Jmhc\Admin\Repository;

class ConfigRepository extends Repository
{
    protected $allowFields = [
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

    protected $orderField  = '';
}
