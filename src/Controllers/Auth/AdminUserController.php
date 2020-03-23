<?php


namespace Jmhc\Admin\Controllers\Auth;

use Illuminate\Routing\Controller;
use Jmhc\Admin\Models\Auth\AdminUser;
use Jmhc\Admin\Repositories\Auth\AdminUserRepository;
use Jmhc\Admin\Services\Auth\AdminUserService;
use Jmhc\Admin\Traits\HasResourceRoutes;

class AdminUserController extends Controller
{
    use HasResourceRoutes;

    /**
     * 分配角色
     * @return mixed
     */
    public function assignRole()
    {
        return $this->service->assignRole();
    }
}
