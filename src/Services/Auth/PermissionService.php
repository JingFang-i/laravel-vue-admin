<?php


namespace Jmhc\Admin\Services\Auth;

use Illuminate\Database\Eloquent\Model;
use Jmhc\Admin\Service;
use Jmhc\Admin\Utils\Helper;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class PermissionService extends Service
{
    protected $multiFields = ['is_menu', 'is_hidden'];
    protected $allPermissions = [];
    protected $exceptAttributes = ['created_at', 'updated_at', 'id', 'children'];

    protected function rules($data, $id): array
    {
        $rules = [
            'title' => ['bail', 'required', 'max:10'],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'icon' => ['max:255'],
            'guard_name' => ['bail', 'required', 'string', 'max:255'],
            'component_path' => ['bail', 'string', 'nullable', 'max:255'],
            'view_route_name' => ['bail', 'string', 'nullable', 'max:50'],
            'view_route_path' => 'max:255',
            'redirect_path' => 'max:80',
            'is_menu' => 'in:0,1|integer',
            'is_hidden' => 'in:0,1|integer',
            'weigh' => 'integer',
            'pid' => 'integer',
        ];

        $unique = Rule::unique('permissions', 'name');
        if (isset($data['guard_name']) && $data['guard_name']) {
            $unique = $unique->where(function($query) use($data){
                $query->where('guard_name', $data['guard_name']);
            });
        }
        if (!is_null($id)) {
            $unique->ignore($id);
        }
        $rules['name'][] = $unique;
        return $rules;
    }

    protected function message(): array
    {
        return [
            'title.required' => '权限标题不能为空',
            'title.max' => '权限标题不能超过10个字',
            'icon.max' => 'icon不能好过',
            'name.required' => '权限名称不能为空',
            'name.max' => '权限名称最多只能为255个字符',
            'name.unique' => '规则已存在',
            'view_route_path.max' => '前端路由路径不能超过255个字符',
            'guard_name.required' => '守卫名称不能为空',
            'guard_name.max' => '守卫名称最多只能为255个字符',
            'component_path.max' => '组件路径不能超过255个字符',
            'component_path.required' => '菜单组件路径不能为空',
            'view_route_name.max' => '前端路由名称不能超过50个字符',
            'view_route_name.required' => '菜单前端路由名称不能为空',
            'is_menu.in' => '是否是菜单值不正确',
            'redirect_path.max' => '跳转路径不能超过80个字符',
            'is_menu.integer' => '是否是菜单值必须为整数',
            'is_hidden.in' => '是否隐藏值不正确',
            'is_hidden.integer' => '是否隐藏值必须为一个整数',
            'weigh.integer' => '权重值必须为一个整数',
            'pid.integer' => '父级ID必须为一个整数',
        ];
    }

    public function index()
    {
        //获取当前用户所有权限
        $collections = $this->repository->allPermissions();
        $roleId = $this->request->input('role_id', 0);
        if ($this->request->has('role_id')) {
            if ($roleId != 1) {
                $role = Role::find($roleId);
                $permissionIds = $this->filterPermission($collections, $role->permissions);
            } else {
                $permissionIds = $collections->pluck('id')->toArray();
            }
            return $this->response->success($permissionIds);
        }
        if ($this->request->has('is_select')) {
            $data = [];
            foreach ($collections as $collection) {
                $data[] = [
                    'id' => $collection['id'],
                    'title' => $collection['title'],
                    'name' => $collection['name'],
                    'pid' => $collection['pid'],
                ];
            }
        } else {
            $data = $collections->toArray();
        }
//        return $this->response->success($this->getTree($data));
        return $this->response->success(Helper::array2Tree($data));
    }

    protected function beforeDestroy(int $id): bool
    {
        // 如果存在子菜单 则不能删除
        if ($this->repository->where('pid', $id)->exists()) {
            $this->errorMsg = '该菜单/权限下存在子菜单或权限，不能删除';
            return false;
        }
        return true;
    }

    /**
     * 添加操作后置
     * @param Model $model
     */
    protected function afterStore(Model $model): void
    {
        $this->repository->updateCache();
    }
    /**
     * 更新操作后置
     * @param Model $model
     */
    protected function afterUpdate(int $id, array $data): void
    {
        $this->repository->updateCache();
    }

    /**
     * 删除操作后置
     * @param Model $model
     */
    protected function afterDestroy(?int $id): void
    {
        $this->repository->updateCache();
    }

    /**
     * 保存前置
     * @param array $data
     * @return array
     */
    protected function beforeStore(array $data): array
    {
        $data['pid'] = isset($data['pid']) && $data['pid'] ? $data['pid'] : 0;
        $data['is_hidden'] = isset($data['is_hidden']) && $data['is_hidden'] ? $data['is_hidden'] : 0;
        $data['is_menu'] = isset($data['is_menu']) && $data['is_menu'] ? $data['is_menu'] : 0;
        return $data;
    }

    /**
     * 过滤掉当前用户没有的权限 只返回最后一级
     *
     * @param $adminPermissions
     * @param $rolePermissions
     * @return mixed
     */
    protected function filterPermission($adminPermissions, $rolePermissions)
    {
        $permissionIds = [];
        $rolePermissions = $rolePermissions->toArray();
        $this->filterParent($rolePermissions);
        foreach ($adminPermissions as $item) {
            foreach ($rolePermissions as $v) {
                if ($item->id == $v['id']) {
                    $permissionIds[] = $v['id'];
                }
            }
        }
        return $permissionIds;
    }

    /**
     * 过滤掉父级，直留下最后一级
     * @param $refData
     */
    private function filterParent(&$refData)
    {
        foreach($refData as $key => $v) {
            foreach ($refData as $key1 => $v1) {
                if ($v['pid'] == $v1['id']) {
                    array_splice($refData, $key1, 1);
                }
            }

        }
    }

    /**
     * 获取菜单和权限
     *
     * @return array
     */
    public function getAuth()
    {
        $allPermission = $this->repository->allPermissions()->toArray(); // 拥有的所有权限
        $this->allPermissions = $allPermission;
        $this->sortPermissions(0, count($allPermission) - 1);
        // 划分成两部分，一部分菜单，一部分页面权限。
        // 每个组件路径下的具体权限（按钮操作）
        $allMenu = [];
        foreach ($this->allPermissions as $item) {
            if ($item['is_menu']) {
                $item['meta'] = [
                    'title' => $item['title'],
                    'icon' => $item['icon'],
                ];
                $allMenu[$item['id']] = $item;
            }
        }
        $menu = Helper::array2Tree($allMenu);
        if (count($menu) > 0) {
            if ($menu[0]['view_route_path'] !== '/') {
                $menu[0]['view_route_path'] = '/';
                if (count($menu[0]['children']) > 0) {
                    $menu[0]['redirect_path'] = '/' . $menu[0]['children'][0]['view_route_path'];
                }
            }
        }
        $data = [
            'menu' => $menu,
            'permission' => $this->getPermissions($allPermission),
        ];

        return $this->response->success($data);
    }

    /**
     * 根据权重重新排序  快速排序算法
     * @param $left
     * @param $right
     */
    protected function sortPermissions($left, $right)
    {
        if ($left > $right) {
            return ;
        }
        $i = $left;
        $j = $right;
        $povitItem = $this->allPermissions[$left];
        $povit = $this->allPermissions[$left]['weigh'];
        while($i != $j) {
            while($i < $j && $this->allPermissions[$j]['weigh'] <= $povit) {
                $j --;
            }
            $this->allPermissions[$i] = $this->allPermissions[$j];
            while($i < $j && $this->allPermissions[$i]['weigh'] >= $povit) {
                $i ++;
            }
            $this->allPermissions[$j] = $this->allPermissions[$i];
        }
        $this->allPermissions[$i] = $povitItem;
        $this->sortPermissions($left, $i - 1);
        $this->sortPermissions($j + 1, $right);
    }

    /**
     * 获取菜单
     *
     * @param array $allMenu
     * @param int $pid
     * @return array
     */
    protected function getTree(array $allMenu, $pid = 0)
    {
        $arr = [];
        foreach ($allMenu as $menu) {
            if ($menu['pid'] == $pid) {
                $menu['children'] = $this->getTree($allMenu, $menu['id']);
                $arr[] = $menu;
            }
        }
        return $arr;
    }

    /**
     * 获取所有操作权限 以视图路由
     *
     * @param $allPermissions
     * @param $allMenu
     * @return array
     */
    protected function getPermissions($allPermissions)
    {
        $permissions = [];
        foreach ($allPermissions as $permission) {
            if (!$permission['is_menu']) {
                $permissions[] = $permission['name'];
            }
        }
        return $permissions;
    }

}
