<?php


namespace Jmhc\Admin\Controllers\Auth;

use Illuminate\Routing\Controller;
use Jmhc\Admin\Repositories\Auth\RoleRepository;
use Jmhc\Admin\Services\Auth\RoleService;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    protected $service;

    public function __construct(Role $role)
    {
        $this->service = new RoleService(new RoleRepository($role));
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
     * 给角色分配权限
     *
     * @return mixed
     */
    public function assignPermission()
    {
        return $this->service->assignPermission();
    }
}
