<?php


namespace App\Services\Auth;

use Jmhc\Admin\Service;
use Jmhc\Admin\Utils\Helper;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class PermissionService extends Service
{
    protected $multiFields = ['is_menu'];
    protected $allPermissions = [];

    protected function rules($data, $id): array
    {
        return [
            'title' => ['bail', 'required', 'max:10'],
            'module_id' => ['bail', 'required', 'integer'],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'component_path' => ['bail', 'string', 'max:255'],
            'view_route_name' => ['bail', 'string', 'max:50'],
            'is_menu' => ['bail', Rule::in(0, 1)],
        ];
    }

    protected function message(): array
    {
        return [
            'title.required' => '权限标题不能为空',
            'title.max' => '权限标题不能超过10个字',
            'module_id.required' => '权限必须关联模块',
            'name.required' => '权限名称不能为空',
            'name.max' => '权限名称最多只能为255个字符',
            'component_path.max' => '组件路径不能超过255个字符',
            'component_path.required' => '菜单组件路径不能为空',
            'view_route_name.max' => '视图路由名称不能超过50个字符',
            'view_route_name.required' => '菜单视图路由名称不能为空',
            'is_menu.in' => '是否是菜单参数值不正确',
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
//                    'module_id' => $item['module_id'],
                ];
                $allMenu[$item['id']] = $item;
            }
        }
        $data = [
//            'menu' => $this->divideByModule($this->getTree($allMenu)),
//            'menu' => $this->getTree($allMenu),
            'menu' => Helper::array2Tree($allMenu),
            'permission' => $this->getPermissions($allPermission, $allMenu),
//            'module' => $this->getModules($allPermission),
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
     * 将菜单按照模块划分（需要module）
     *
     * @param $allMenu
     * @return array
     */
    protected function divideByModule($allMenu)
    {
        $dividedMenu = [];
        foreach($allMenu as $menu) {

            if (array_key_exists($menu['module_id'], $dividedMenu)) {
                $dividedMenu[$menu['module_id']][] = $menu;
            } else {
                $dividedMenu[$menu['module_id']] = [$menu];
            }
        }
        return $dividedMenu;
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
    protected function getPermissions($allPermissions, $allMenu)
    {
        $permissionsMap = [];
        foreach ($allPermissions as $permission) {
            if (!$permission['is_menu'] && array_key_exists($permission['pid'], $allMenu)) {
                if(array_key_exists($allMenu[$permission['pid']]['view_route_name'], $permissionsMap)) {
                    $permissionsMap[$allMenu[$permission['pid']]['view_route_name']] = [$permission];
                } else {
                    $permissionsMap[$allMenu[$permission['pid']]['view_route_name']][] = $permission;
                }
            }
        }
        return $permissionsMap;
    }

    /**
     * 获取拥有的所有模块（需要module）
     *
     * @param $allPermissions
     * @return array
     */
    protected function getModules($allPermissions)
    {
        $moduleIds = array_unique(array_column($allPermissions, 'module_id'));
        $allModules = Module::where('status', 1)
            ->orderBy('weigh', 'desc')
            ->select('id', 'name', 'icon')
            ->find($moduleIds);
        return $allModules ? $allModules : [];
    }
}
