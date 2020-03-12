<?php


namespace Jmhc\Admin\Repositories\Auth;

use Jmhc\Admin\Repository;

class PermissionRepository extends Repository
{
    /**
     * 获取当前用户所有权限
     *
     * @return mixed
     */
    public function allPermissions($adminId = null)
    {
        if (is_null($adminId)) {
            $user = auth('admin')->user();
        } else {
            $user = AdminUser::find($adminId);
        }
        if ($user->hasRole('admin')) {
            return $this->selectList(['*'], [['guard_name', '=', 'admin']]);
        } else {
            return $user->getAllPermissions();
        }
    }
}
