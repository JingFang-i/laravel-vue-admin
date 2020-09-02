<?php


namespace Jmhc\Admin\Traits;


use Illuminate\Database\Eloquent\Model;

trait HasResourceActions
{

    protected $exceptAttributes = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $defaultWith = [];

    /**
     * 查看
     *
     * @return mixed
     */
    public function index()
    {
        if (! $this->request->has('is_select')) {
            $lists = $this->repository->lists($this->request->query(), $this->defaultWith, $this->buildWhere());
            if (!is_null($this->transformer)) {
                return $this->response->paginator($lists, $this->transformer);
            } else {
                return $this->response->paginator($lists);
            }
        } else {
            return $this->response->collection($this->repository->selectList());
        }
    }

    protected function buildWhere()
    {
        return [];
    }

    /**
     * 新增
     */
    public function store()
    {
        $formData = $this->request->all();
        if (!is_null($this->validator)) {
            $formData = $this->validator->validated();
        }
        if (!$this->validate($formData)) {
            return $this->response->error($this->errorMsg);
        }
        $formData = $this->beforeStore($formData);

        $model = $this->repository->store($formData);
        if ($model) {
            $this->afterStore($model);
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
        $formData = $this->request->except($this->exceptAttributes);
        if (!is_null($this->validator)) {
            $formData = $this->validator->validated();
        }
        if (!$this->validate($formData, $id)) {
            return $this->response->error($this->errorMsg);
        }
        $formData = $this->beforeUpdate($id, $formData);

        if ($this->repository->update($id, $formData)) {
            $this->afterUpdate($id, $formData);
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
            $this->afterDestroy($id);
            return $this->response->success();
        } else {
            return $this->response->error();
        }
    }

    /**
     * 排序
     * @return mixed
     */
    public function sort()
    {
        $oldId = intval($this->request->input('old_id'));
        $newId = intval($this->request->input('new_id'));

        $oldRow = $this->repository->find($oldId);
        $newRow = $this->repository->find($newId);

        if (empty($oldRow) || empty($newRow)) {
            return $this->response->error('记录不存在');
        }

        $oldWeigh = $oldRow->weigh;
        $oldRow->weigh = $newRow->weigh;
        $newRow->weigh = $oldWeigh;

        $oldRow->save();
        $newRow->save();
        return $this->response->success();
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
     * 保存后置方法
     * @param Model $model
     */
    protected function afterStore(Model $model): void
    {}

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
     * 更新后置方法
     * @param int $id
     * @param array $data
     */
    protected function afterUpdate(int $id, array $data): void
    {}

    /**
     * 删除前置方法
     * @param int $id
     * @return bool
     */
    protected function beforeDestroy(int $id): bool
    {
        return true;
    }

    /**
     * 删除后置方法
     * @param int $id
     */
    protected function afterDestroy(int $id): void
    {}

}
