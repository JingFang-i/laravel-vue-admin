<?php


namespace Jmhc\Admin\Services\Auth;


use Jmhc\Admin\Models\Auth\AdminUser;
use Jmhc\Admin\Service;
use Jmhc\Admin\Utils\Helper;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService extends Service
{
    protected function rules($data, $id): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255'],
            'parent_id' => ['bail', 'required', 'integer'],
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => '请输入角色名称',
            'name.max' => '角色名称不能超过255个字符',
            'parent_id.required' => '请选择父级',
            'parent_id.integer' => '父级格式不正确',
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
            $roleIds = array_column($this->repository->getUserAllRoles(intval($adminId)), 'id');
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
     * 获取角色列表
     * @return array
     */
    private function getRoleList()
    {
        //如果当前用户是超级管理员，则可以获取所有角色
        //如果是其他用户，则只获取其下面的角色
        if ($this->user()->hasRole('admin')) {
            $allRole = $this->repository->all()->toArray();
        } else {
            $allRole = $this->repository->getUserAllRoles($this->user()->id);
        }
        if ($this->request->has('is_select')) {
            $allRole = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'parent_id' => $item['parent_id'],
                    'name' => $item['name'],
                ];
            }, $allRole);
        }
        return Helper::array2Tree($allRole, 'parent_id', $allRole[0]['parent_id'], false);

    }

    /**
     * 新增
     */
    public function store()
    {
        $formData = $this->request->all();
        $formData = $this->beforeStore($formData);
        if (!$this->validate($formData)) {
            return $this->response->error($this->errorMsg);
        }
        $exists = $this->repository->where('guard_name', $this->guardName())
            ->where('name', $formData['name'])
            ->exists();
        if ($exists) {
            return $this->response->error('该角色已存在');
        }
        $model = $this->repository->store($formData);
        if ($model) {
            $this->afterStore($model);
            return $this->response->success(['id' => $model->id]);
        } else {
            return $this->response->error();
        }
    }

    /**
     * 更新
     */
    public function update(int $id)
    {
        if ($id == 1) {
            return $this->response->error('此角色不允许修改');
        }
        if ($this->user()->hasRole($id) && !$this->user()->hasRole('admin')) {
            return $this->response->error('您不能修改该角色');
        }
        $formData = $this->request->except($this->exceptAttributes);
        if (!$this->validate($formData, $id)) {
            return $this->response->error($this->errorMsg);
        }
        $exists = $this->repository->where('guard_name', $this->guardName())
            ->where('name', $formData['name'])
            ->where('id', '<>', $id)
            ->exists();
        if ($exists) {
            return $this->response->error('该角色已存在');
        }
        if ($this->repository->update($id, $formData)) {
            return $this->response->success();
        } else {
            return $this->response->error();
        }
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
        if ($this->user()->hasRole($id) && !$this->user()->hasRole('admin')) {
            $this->setError('您不能删除该角色');
            return false;
        }
        $userIds = DB::table('model_has_roles')->where('role_id', $id)->pluck('model_id');

        if (AdminUser::whereIn('id', $userIds)->exists()) {
            $this->errorMsg = '该角色下存在用户，不能删除';
            return false;
        }
        if (Role::where('parent_id', $id)->exists()) {
            $this->errorMsg = '该角色下存在其他角色，不能删除';
            return false;
        }
        return true;
    }


}
