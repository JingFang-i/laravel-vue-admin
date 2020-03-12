<?php


namespace Jmhc\Admin\Contracts;


interface Repository
{

    /**
     * 查看查询
     * @param array $params
     * @return mixed
     */
    public function lists(array $params, array $with = [], array $where = []);

    /**
     * 新增
     * @param array $data
     * @return mixed
     */
    public function store(array $data);

    /**
     * 更新
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * 查询一条记录
     *
     * @param $id
     * @return mixed
     */
    public function show(int $id, array $with = [], array $fields = []);

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function destroy(int $id);

    /**
     * 批量删除
     * @param array $ids
     * @return mixed
     */
    public function multiDestroy(array $ids);

}
