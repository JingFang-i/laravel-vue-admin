<?php


namespace Jmhc\Admin\Traits;


use Jmhc\Admin\Utils\Helper;

trait HasMultiEdit
{

    protected $multiFields = ['status'];

    /**
     * 批量修改
     * @return mixed
     */
    public function multi()
    {
        $params = $this->request->only(['ids', 'data']);
        if (!isset($params['ids'])) {
            return $this->response->error('缺少需要更新记录的ID');
        }
        $ids = Helper::parseIds($params['ids']);
        if (empty($ids)) {
            return $this->response->error('请选择要更新的记录');
        }
        //过滤字段，只有定义在multiFields的字段可以更新
        $updateData = array_intersect_key($params['data'], array_flip($this->multiFields));
        if (empty($updateData)) {
            return $this->response->error('没有可更新的字段');
        }
        $methodExists = method_exists($this, 'afterUpdate');
        if (count($ids) === 1) {
            $this->repository->update($ids[0], $updateData);
            if ($methodExists) {
                $this->afterUpdate($ids[0], $updateData);
            }
        } else {
            $rows = $this->repository->whereIn('id', $ids)->get();
            foreach ($rows as $row) {
                $row->fill($updateData)->save();
                if ($methodExists){
                    $this->afterUpdate($row->id, $updateData);
                }
            }
        }

        return $this->response->success();
    }
}
