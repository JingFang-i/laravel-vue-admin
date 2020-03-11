<?php


namespace Jmhc\Admin\Repositories\Auth;

use Jmhc\Admin\Library\Repository;

class AdminUserRepository extends Repository
{
    protected $orderField = 'id';

    protected $orderType = 'asc';
}
