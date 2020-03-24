<?php


namespace Jmhc\Admin\Controllers\Auth;


use Illuminate\Routing\Controller;
use Jmhc\Admin\Repositories\Auth\PermissionRepository;
use Jmhc\Admin\Services\Auth\PermissionService;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    protected $service;

    public function __construct(Permission $permission)
    {
        $permissionRepository = new PermissionRepository($permission);
        $this->service = new PermissionService($permissionRepository);
    }

    /**
     * 查看
     *
     */
    public function index()
    {
        return $this->service->index();
    }

    /**
     * 新增
     */
    public function store()
    {
        return $this->service->store();
    }

    /**
     * 更新
     */
    public function update($id)
    {
        return $this->service->update($id);
    }

    /**
     * 查询一条记录
     *
     */
    public function show($id)
    {
        return $this->service->show($id);
    }

    /**
     * 删除
     */
    public function destroy($id)
    {
        return $this->service->destroy($id);
    }

    /**
     * 批量修改
     * @return mixed
     */
    public function multi()
    {
        return $this->service->multi();
    }

    /**
     * 批量删除
     * @return mixed
     */
    public function multiDestroy()
    {
        return $this->service->multiDestroy();
    }

    /**
     * 菜单和权限
     *
     */
    public function auth()
    {
        return $this->service->getAuth();
    }

}
