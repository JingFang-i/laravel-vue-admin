<?php


namespace App\Repositories;

use Jmhc\Admin\Repository;

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
