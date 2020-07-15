<?php


namespace Jmhc\Admin\Services\System;

use Jmhc\Admin\Service;

class DictionaryService extends Service
{
    protected function rules(array $data, $id): array
    {
        return [
            'title' => 'bail|required|max:15',
            'name' => 'bail|required|max:15|alphaDash',
            'describe' => 'max:255',
            'value' => 'array',
        ];
    }

    protected function message(): array
    {
        return [
            'title.required' => '字典名称不能为空',
            'title.max' => '字典名称不能超过15个字符',
            'name.required' => '字典标识不能为空',
            'name.max' => '字典表不能超过15个字符',
            'name.alpha' => '字典标识只能为字母',
            'value.array' => '字典值必须为键值对',
        ];
    }

    /**
     * 获取字典内容
     * @param string $name
     * @return mixed
     */
    public function getDict(string $name)
    {
        $dict = $this->repository->where('name', $name)->value('value');
        $dict = empty($dict) ? [] : $dict;
        return $this->response->success($dict);
    }

    public function beforeDestroy(int $id): bool
    {
        if($id == 1) {
            $this->setError('不能删除此项');
            return false;
        }
        return true;
    }
}
