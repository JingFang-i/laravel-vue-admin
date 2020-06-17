<?php


namespace Jmhc\Admin\Traits;


use Jmhc\Admin\Contracts\Service;

trait HasResourceRoutes
{

    /**
     * 查看
     *
     */
    public function index(Service $service)
    {
        return $service->index();
    }

    /**
     * 新增
     */
    public function store(Service $service)
    {
        return $service->store();
    }

    /**
     * 更新
     */
    public function update(Service $service, $id)
    {
        return $service->update($id);
    }

    /**
     * 查询一条记录
     *
     */
    public function show(Service $service, $id)
    {
        return $service->show($id);
    }

    /**
     * 删除
     */
    public function destroy(Service $service, $id)
    {
        return $service->destroy($id);
    }

    /**
     * 批量修改
     * @return mixed
     */
    public function multi(Service $service)
    {
        return $service->multi();
    }

    /**
     * 批量删除
     * @return mixed
     */
    public function multiDestroy(Service $service)
    {
        return $service->multiDestroy();
    }

    /**
     * 排序
     * @param Service $service
     * @return mixed
     */
    public  function sort(Service $service)
    {
        return $service->sort();
    }
}
