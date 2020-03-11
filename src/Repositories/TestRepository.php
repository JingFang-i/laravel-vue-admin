<?php


namespace Jmhc\Admin\Repositories;

use Jmhc\Admin\Library\Repository;

class TestRepository extends Repository
{
    protected $allowFields = [
        'id',
        'name',
    ];

    protected $orderField  = '';
}
