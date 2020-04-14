<?php


namespace Jmhc\Admin\Controllers\Auth;

use Illuminate\Routing\Controller;
use Jmhc\Admin\Contracts\Service;
use Jmhc\Admin\Traits\HasResourceRoutes;

class AdminUserController extends Controller
{
    use HasResourceRoutes;

    /**
     * 分配角色
     * @return mixed
     */
    public function assignRole(Service $service)
    {
        return $service->assignRole();
    }
}
