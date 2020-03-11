<?php


namespace Jmhc\Admin\Traits;


trait HasResourceActions
{

    protected $exceptAttribute = [
        'created_at', 'updated_at', 'deleted_at'
    ];


    /**
     * 查看
     *
     * @return mixed
     */
    public function index()
    {
        if (! $this->request->has('is_select')) {
            $lists = $this->repository->lists($this->request->query());
            if (!is_null($this->transformer)) {
                return $this->response->paginator($lists, $this->transformer);
            } else {
                return $this->response->paginator($lists);
            }
        } else {
            return $this->response->collection($this->repository->selectList());
        }
    }

    /**
     * 新增
     */
    public function store()
    {
        $formData = $this->request->all();
        $formData = $this->beforeStore($formData);
        if (!is_null($this->validator)) {
            $formData = $this->validator->validated();
        }
        if (!$this->validate($formData)) {
            return $this->response->error($this->errorMsg);
        }
        $model = $this->repository->store($formData);
        if ($model) {
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
        $formData = $this->request->except($this->exceptAttribute);
        $formData = $this->beforeUpdate($id, $formData);
        if (!is_null($this->validator)) {
            $formData = $this->validator->validated();
        }
        if (!$this->validate($formData, $id)) {
            return $this->response->error($this->errorMsg);
        }
        if ($this->repository->update($id, $formData)) {
            return $this->response->success();
        } else {
            return $this->response->error();
        }
    }

    /**
     * 查询一条记录
     *
     * @param $id
     * @return mixed
     */
    public function show(int $id)
    {
        $row = $this->repository->show($id);
        if ($row) {
            return $this->response->item($row);
        } else {
            return $this->response->queryNull();
        }
    }

    /**
     * 删除
     */
    public function destroy(int $id)
    {
        if (!$this->beforeDestroy($id)) {
            return $this->response->error($this->errorMsg);
        }
        if ($this->repository->destroy($id)) {
            return $this->response->success();
        } else {
            return $this->response->error();
        }
    }

    /**
     * 保存前置方法
     * @param $data
     * @return array $data
     */
    protected function beforeStore(array $data): array
    {
        return $data;
    }

    /**
     * 更新前置方法
     * @param $data
     * @return array
     */
    protected function beforeUpdate(int $id, array $data): array
    {
        return $data;
    }



    /**
     * 删除前置方法
     * @param int $id
     * @return bool
     */
    protected function beforeDestroy(int $id): bool
    {
        return true;
    }

}
