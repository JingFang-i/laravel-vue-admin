<?php


namespace Jmhc\Admin\Repositories\Auth;

use Illuminate\Support\Facades\DB;
use Jmhc\Admin\Repository;

class AdminUserRepository extends Repository
{
    protected $orderField = 'id';

    protected $orderType = 'asc';

    public function getAdminUserByRoleIds(array $roleIds, $with = [])
    {
        $userIds = DB::table('model_has_roles')
            ->whereIn('role_id', $roleIds)
            ->pluck('model_id')
            ->toArray();
        return $this->model
            ->when($with, function($query, $with) {
                $query->with($with);
            })
            ->whereIn('id', $userIds)
            ->paginate(self::PER_PAGE);
    }
}
