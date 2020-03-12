<?php


namespace App\Http\Admin\Controllers\v1\Auth;


use App\Http\Admin\Controllers\Controller;
use App\Models\Auth\AdminUser;
use App\Repositories\Auth\AdminUserRepository;
use App\Services\Auth\AdminUserService;

class AdminController extends Controller
{
    protected $service;

    public function __construct(AdminUser $adminUser)
    {
        $adminUserRepository = new AdminUserRepository($adminUser);
        $this->service = new AdminUserService($adminUserRepository);
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
     * 分配角色
     * @return mixed
     */
    public function assignRole()
    {
        return $this->service->assignRole();
    }
}
