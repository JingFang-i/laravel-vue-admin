<?php


namespace Jmhc\Admin\Services\Auth;


use Jmhc\Admin\Service;

class ModuleService extends Service
{
    protected $multiFields = ['status'];

    protected function rules($data, $id): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:15'],
            'weigh' => ['integer'],
            'status' => ['integer', 'in:0,1'],
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => '请输入名称',
            'name.max' => '名称不能超过15个字符',
        ];
    }
}
