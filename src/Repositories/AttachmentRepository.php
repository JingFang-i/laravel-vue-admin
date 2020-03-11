<?php


namespace Jmhc\Admin\Repositories;

use Jmhc\Admin\Library\Repository;

class AttachmentRepository extends Repository
{

    /**
     * 保存
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }


}
