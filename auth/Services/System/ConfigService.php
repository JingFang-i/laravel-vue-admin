<?php


namespace App\Services\System;

use Jmhc\Admin\Traits\HasResourceActions;
use Jmhc\Admin\Service;

class ConfigService extends Service
{
    use HasResourceActions;

    protected function rules(array $data, $id): array
    {
        return [
            'group' => 'bail|required|max:15',
            'title' => 'bail|required|max:50',
            'name' => 'bail|required|max:50',
            'type' => 'bail|required|in:string,text,editor',
        ];
    }

    protected function message(): array
    {
        return [
            'group.required' => '配置组不能为空',
            'group.max' => '配置组不能超过15个字符',
            'title.required' => '配置名称不能为空',
            'title.max' => '配置名称不能超过50个字符',
            'name.required' => '标识符不能为空',
            'name.max' => '标识符不能超过50个字符',
            'type.required' => '配置类型不能为空',
            'type.in' => '配置类型只能为字符串,文本,编辑器',
        ];
    }

    public function index()
    {
        $configs = $this->repository->all()->toArray();
        $regroupConfigs = [];
        foreach ($configs as $config) {
            if (!array_key_exists($config['group'], $regroupConfigs)) {
                $regroupConfigs[$config['group']] = [$config];
            } else {
                $regroupConfigs[$config['group']][] = $config;
            }
        }
        return $this->response->success($regroupConfigs);
    }

    /**
     * 更新一个组
     */
    public function updateGroup()
    {
        $configs = $this->request->all();
        $filtered = array_filter($configs, function ($item) {
            return array_key_exists('id', $item) && array_key_exists('value', $item)
                && $item['id'];
        });
        $ids = array_column($filtered, 'id');
        $regroupData = [];
        foreach ($filtered as $item){
            $regroupData[$item['id']] = $item['value'];
        }
        $all = $this->repository->whereIn('id', $ids)->get();
        foreach ($all as $item) {
            $item->value = $regroupData[$item->id];
            $item->save();
        }
        return $this->response->success();
    }
}
