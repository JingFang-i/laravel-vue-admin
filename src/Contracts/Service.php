<?php


namespace Jmhc\Admin\Contracts;


interface Service
{
    /**
     * 数据实体集合
     * @return mixed
     */
    public function index();

    /**
     * 实体详情
     * @param int $id
     * @return mixed
     */
    public function show(int $id);

    /**
     * 保存实数据
     * @return mixed
     */
    public function store();

    /**
     * 更新数据
     * @param int $id
     * @return mixed
     */
    public function update(int $id);

    /**
     * 删除数据
     * @param int $id
     * @return mixed
     */
    public function destroy(int $id);
}
