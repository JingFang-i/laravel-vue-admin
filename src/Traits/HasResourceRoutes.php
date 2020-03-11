<?php


namespace Jmhc\Admin\Traits;


use Jmhc\Admin\Contracts\ServiceInterface;

trait HasResourceRoutes
{

    /**
     * 查看
     *
     */
    public function index(ServiceInterface $service)
    {
        return $service->index();
    }

    /**
     * 新增
     */
    public function store(ServiceInterface $service)
    {
        return $service->store();
    }

    /**
     * 更新
     */
    public function update(ServiceInterface $service, $id)
    {
        return $service->update($id);
    }

    /**
     * 查询一条记录
     *
     */
    public function show(ServiceInterface $service, $id)
    {
        return $service->show($id);
    }

    /**
     * 删除
     */
    public function destroy(ServiceInterface $service, $id)
    {
        return $service->destroy($id);
    }

    /**
     * 批量修改
     * @return mixed
     */
    public function multi(ServiceInterface $service)
    {
        return $service->multi();
    }

    /**
     * 批量删除
     * @return mixed
     */
    public function multiDestroy(ServiceInterface $service)
    {
        return $service->multiDestroy();
    }
}
