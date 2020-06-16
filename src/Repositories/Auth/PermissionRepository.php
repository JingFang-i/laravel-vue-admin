<?php


namespace Jmhc\Admin\Repositories\Auth;

use Illuminate\Support\Facades\Cache;
use Jmhc\Admin\Repository;

class PermissionRepository extends Repository
{
    const ALL_PERMISSION_CACHE_PREFIX = 'all_permissions';

    /**
     * 获取当前用户所有权限
     *
     * @return mixed
     */
    public function allPermissions()
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            if (Cache::has(self::ALL_PERMISSION_CACHE_PREFIX)){
                return Cache::get(self::ALL_PERMISSION_CACHE_PREFIX);
            } else {
                $permissions = $this->model->where('guard_name', auth()->getDefaultDriver())->get();
                Cache::put(self::ALL_PERMISSION_CACHE_PREFIX, $permissions);
                return $permissions;
            }
        } else {
            return $user->getAllPermissions();
        }
    }

    /**
     * 更新所有权限的缓存
     */
    public function updateCache()
    {
        Cache::forget(self::ALL_PERMISSION_CACHE_PREFIX);
        $permissions = $this->model->where('guard_name', auth()->getDefaultDriver())->get();
        Cache::put(self::ALL_PERMISSION_CACHE_PREFIX, $permissions);
    }
}
