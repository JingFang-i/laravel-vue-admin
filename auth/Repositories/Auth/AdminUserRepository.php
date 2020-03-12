<?php


namespace App\Repositories\Auth;

use Jmhc\Admin\Repository;

class AdminUserRepository extends Repository
{
    protected $orderField = 'id';

    protected $orderType = 'asc';
}
