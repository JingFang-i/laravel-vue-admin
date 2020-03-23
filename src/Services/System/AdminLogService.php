<?php


namespace Jmhc\Admin\Services\System;


use Jmhc\Admin\Service;

class AdminLogService extends Service
{
    protected $multiFields = [];



    protected function rules($data, $id): array
    {
        return [
            'admin_id' => [
                'required',
                'numeric',
            ],
            'name' => [
                'required',
                'max:20',
            ],
            'title' => [
                'max:255',
            ],
            'ip' => [
                'numeric',
            ],
        ];
    }

    protected function message(): array
    {
        return [
            'admin_id.numeric' => '管理员ID必须为一个数字',
            'admin_id.required' => '管理员ID不能为空',
            'name.max' => '姓名不能超过20个字符',
            'name.required' => '姓名不能为空',
            'title.max' => '标题不能超过255个字符',
            'ip.numeric' => 'IP必须为一个数字',
        ];
    }


}
