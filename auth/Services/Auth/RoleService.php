<?php


namespace App\Services\Auth;


use App\Models\Auth\AdminUser;
use Jmhc\Admin\Service;
use Jmhc\Admin\Utils\Helper;
use Illuminate\Support\Facades\DB;

class RoleService extends Service
{
    protected function rules($data, $id): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255'],
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => '请输入角色名称',
            'name.max' => '角色名称不能超过255个字符',
        ];
    }

    /**
     * 查看
     *
     * @return mixed
     */
    public function index()
    {
        if ($this->request->has('admin_id')) {
            $adminId = $this->request->input('admin_id');
            $roleIds = $this->getAdminRoleIds(intval($adminId));
            return $this->response->success($roleIds);
        }

        $roleList = $this->getRoleList();
        return $this->response->success($roleList);
    }

    /**
     * 分配权限
     *
     * @return mixed
     */
    public function assignPermission()
    {
        $roleId = $this->request->input('role_id');
        $permissionIds = $this->request->input('permission_ids');
        if (!is_array($permissionIds)) {
            return $this->response->error('权限请求参数格式不正确');
        }
        $roleId = intval($roleId);
        if ($roleId === 1) {
            return $this->response->error('超级管理员权限禁止进行分配');
        }
        if (empty($permissionIds)) {
            return $this->response->error('权限不能为空');
        }
        $permissionIds = array_map(function ($id) {
            return intval($id);
        }, $permissionIds);

        $role = $this->repository->getById($roleId);
        if ($role->syncPermissions($permissionIds)) {
            return $this->response->success();
        } else {
            return $this->error('权限分配失败');
        }
    }

    /**
     * 获取管理员角色
     * @param int $adminId
     * @return array
     */
    public function getAdminRoleIds(int $adminId): array
    {
        $admin = AdminUser::find($adminId);
        if (!$admin) {
            return [];
        }
        return $admin->roles()->pluck('id')->toArray();
    }

    /**
     * 获取角色列表
     * @return array
     */
    private function getRoleList()
    {
        //如果当前用户是超级管理员，则可以获取所有角色
        //如果是其他用户，则只获取其下面的角色
        if ($this->user->hasRole(1)) {
            $allRole = $this->repository->all();
        } else {
            $allRole = $this->repository->roles(); //当前用户拥有的角色
        }
        if ($this->request->has('is_select')) {
            $allRole = $allRole->map(function($item) {
                return $item->only(['id', 'parent_id', 'name']);
            });
        }
        return Helper::array2Tree($allRole->toArray(), 'parent_id', 0, false);

    }

    /**
     * 处理输入的值
     * @param $data
     * @return array
     */
    protected function beforeStore(array $data): array
    {
        $data['parent_id'] = $this->user->id;
        return $data;
    }

    /**
     * 删除前置方法
     * @param int $id
     * @return bool
     */
    protected function beforeDestroy(int $id): bool
    {
        //如果存在用户则不能删除该角色，超级管理员不能删除
        if ($id == 1) {
            $this->errorMsg = '该角色不能删除';
            return false;
        }
        $userIds = DB::table('model_has_roles')->where('role_id', $id)->pluck('model_id');

        if (AdminUser::whereIn('id', $userIds)->exists()) {
            $this->errorMsg = '该角色下存在用户，不能删除';
            return false;
        }
        return true;
    }


}
