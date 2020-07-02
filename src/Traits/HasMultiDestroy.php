<?php


namespace Jmhc\Admin\Traits;


trait HasMultiDestroy
{


    /**
     * 批量删除
     * @return mixed
     */
    public function multiDestroy()
    {
        $params = $this->request->only(['ids']);
        if (!isset($params['ids'])) {
            return $this->response->error('缺少需要删除记录的ID');
        }
        if (is_string($params['ids'])) {
            $params['ids'] = explode(',', $params['ids']);
        }
        if($this->repository->multiDestroy($params['ids'])){
            if (method_exists($this, 'afterDestroy')) {
                foreach ($params['ids'] as $id) {
                    $this->afterDestroy($id);
                }
            }
            return $this->response->success();
        } else {
            return $this->response->error('没有任何记录被删除');
        }
    }
}
