<?php


namespace Jmhc\Admin\Repositories\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Jmhc\Admin\Repository;

class ConfigRepository extends Repository
{
    const CACHE_CONFIG_GROUP_PREFIX = 'config_group:';

    public function __construct(Model $model)
    {
        parent::__construct($model);

        config([
            'admin.model_prefix' => "Jmhc\\Admin\\Models",
            'admin.repository_prefix' => "Jmhc\\Admin\\Repositories",
            'admin.service_prefix' => "Jmhc\\Admin\\Services",
        ]);
    }

    public function getCachedGroupConfigs(string $groupName)
    {
        $cachedKey = $this->getCachedKey($groupName);
        return Cache::get($cachedKey);
    }

    public function updateCachedGroupConfigs(string $groupName, array $groupConfigs)
    {
        $cachedKey = $this->getCachedKey($groupName);
        return Cache::put($cachedKey, $groupConfigs);
    }

    /**
     * 获取组配置
     * @param string $groupName
     * @return mixed
     */
    public function getGroupConfigs(string $groupName): array
    {
        $cachedKey = $this->getCachedKey($groupName);
        $groupConfigs = $this->getCachedGroupConfigs($cachedKey);
        if (empty($groupConfigs)) {
            $groupConfigs = $this->model->where('group', $groupName)->pluck('value', 'name')->toArray();
            if (!empty($groupConfigs)) {
                $this->updateCachedGroupConfigs($groupName, $groupConfigs);
            }
        }
        return $groupConfigs;
    }

    /**
     * 更新组配置中的一个配置项
     * @param string $groupName
     * @param string $name
     * @param $value
     */
    public function updateCachedConfig(string $groupName, string $name, $value)
    {
        $groupConfigs = $this->getGroupConfigs($groupName);
        if (!empty($groupConfigs)) {
            $groupConfigs[$name] = $value;
        }
        $this->updateCachedGroupConfigs($groupName, $groupConfigs);
    }

    protected function getCachedKey(string $groupName)
    {
        return self::CACHE_CONFIG_GROUP_PREFIX . $groupName;
    }
}
