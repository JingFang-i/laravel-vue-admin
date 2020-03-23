<?php


namespace Jmhc\Admin\Repositories\Auth;

use Illuminate\Support\Facades\DB;
use Jmhc\Admin\Repository;

class RoleRepository extends Repository
{
    public function getUserAllRoles(int $userId)
    {
        $userRoleIds = DB::table('model_has_roles')
            ->where('model_id', $userId)
            ->pluck('role_id')
            ->toArray();
        $userRoles = $this->model->whereIn('id', $userRoleIds)->get()->toArray();
        return array_merge($userRoles, $this->_findAllChildren($userRoleIds));
    }

    private function _findAllChildren(array $parentIds)
    {
        $data = [];
        if (!empty($parentIds)) {
            $found = $this->model->whereIn('parent_id', $parentIds)->get();
            if (!empty($found)) {
                $found = $found->toArray();
                $data = array_merge($data, $found);
                $foundIds = array_column($found, 'id');
                $children = $this->_findAllChildren($foundIds);
                if (!empty($children)) {
                    $data = array_merge($data, $children);
                }
            }

        }
        return $data;
    }
}
