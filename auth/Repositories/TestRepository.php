<?php


namespace App\Repositories;

use Jmhc\Admin\Repository;

class TestRepository extends Repository
{
    protected $allowFields = [
        'id',
        'name',
    ];

    protected $orderField  = '';
}
