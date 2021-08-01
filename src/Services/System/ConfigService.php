<?php


namespace Jmhc\Admin\Services\System;

use Jmhc\Admin\Service;

class ConfigService extends Service
{

    const DISABLE_DELETE = ['name', 'logo']; //禁止删除


    protected function rules(array $data, $id): array
    {
        return [
            'group' => 'bail|required|max:25',
            'title' => 'bail|required|max:50',
            'name' => 'bail|required|max:50',
            'type' => 'bail|required|in:string,text,number,editor,switch,image,images',
            'tips' => 'max:255',
            'value' => 'max:65535',
            'extend' => 'max:255',
            'rule' => 'max:255',
        ];
    }

    protected function message(): array
    {
        return [
            'group.required' => '配置组不能为空',
            'group.max' => '配置组不能超过25个字符',
            'title.required' => '配置名称不能为空',
            'title.max' => '配置名称不能超过50个字符',
            'name.required' => '标识符不能为空',
            'name.max' => '标识符不能超过50个字符',
            'type.required' => '配置类型不能为空',
            'type.in' => '配置类型只能为字符串,文本,数字,编辑器,开关,图片',
            'tips.max' => '提示不能超过255个字符',
            'value.max' => '配置值不能超过65535个字符',
            'extend.max' => '扩展数据不能超过255个字符',
            'rule.max' => '验证规则不能超过255个字符',
        ];
    }

    public function index()
    {
        $configs = $this->repository->all()->toArray();
        $regroupConfigs = [];

        foreach ($configs as $config) {
            $config['value'] = $config['type'] === 'images'
                ? json_decode($config['value'], true)
                : $config['value'];
            if (!isset($regroupConfigs[$config['group']])) {
                $regroupConfigs[$config['group']] = [$config];
            } else {
                $regroupConfigs[$config['group']][] = $config;
            }
        }
        return $this->response->success($regroupConfigs);
    }

    /**
     * 保存
     * @return mixed
     */
    public function store()
    {
        $formData = $this->request->all();
        if (!$this->validate($formData)) {
            return $this->response->error($this->errorMsg);
        }
        $exists = $this->repository
            ->where('group', $formData['group'])
            ->where('name', $formData['name'])
            ->exists();
        if ($exists) {
            return $this->response->error('该变量名称已存在！');
        }
        if ($formData['type'] === 'images') {
            $formData['value'] = json_encode($formData['value']);
        }
        $model = $this->repository->store($formData);
        if ($model) {
            // 更新
            $this->repository->updateCachedConfig($model->group, $model->name, $model->value);
            return $this->response->success(['id' => $model->id]);
        } else {
            return $this->response->error();
        }
    }

    /**
     * 更新一个组
     */
    public function updateGroup()
    {
        $group = $this->request->input('group');
        if (!$group) {
            return $this->response->error('组名不能为空');
        }
        $formData = $this->request->input('rows');
        $all = $this->repository->where('group', $group)->get();
        foreach ($all as $item) {
            if (isset($formData[$item->name])) {
                if ($item->type === 'images') {
                    $item->value = json_encode($formData[$item->name]);
                } else {
                    $item->value = $formData[$item->name];
                }
                $item->save();
            }
        }
        $groupConfigs = $all->where('group', $group)->pluck('value', 'name')->toArray();
        $this->repository->updateCachedGroupConfigs($group, $groupConfigs);
        return $this->response->success();
    }

    /**
     * 获取站点配置
     * @return mixed
     */
    public function getWebsiteConfig()
    {
        $configs = $this->repository->getGroupConfigs('website');
        return $this->response->success($configs);
    }

    /**
     * 获取组配置
     * @param string $groupName
     * @return mixed
     */
    public function getGroupConfig(string $groupName)
    {
        $configs = $this->repository->getGroupConfigs($groupName);

        return $this->response->success($configs);
    }

    /**
     * 删除前置
     * @param int $id
     * @return bool
     */
    protected function beforeDestroy(int $id): bool
    {
        $config = $this->repository->find($id);
        if (in_array($config->name, self::DISABLE_DELETE)) {
            $this->setError('该配置不允许删除');
            return false;
        }
        return true;
    }


}
