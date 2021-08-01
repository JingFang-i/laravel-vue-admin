<?php


namespace Jmhc\Admin\Repositories\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Jmhc\Admin\Repository;

class ConfigRepository extends Repository
{
    const CACHE_CONFIG_GROUP_PREFIX = 'config_group:';
    const CONFIG_TTL = 86400 * 15;

    public function getCachedGroupConfigs(string $groupName)
    {
        $cachedKey = $this->getCachedKey($groupName);
        return Cache::get($cachedKey);
    }

    public function updateCachedGroupConfigs(string $groupName, array $groupConfigs)
    {
        $cachedKey = $this->getCachedKey($groupName);
        return Cache::put($cachedKey, $groupConfigs, self::CONFIG_TTL);
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
            $groupConfigs = $this->model->where('group', $groupName)->get();
            $regroupArr = [];
            foreach ($groupConfigs as $groupConfig) {
                $regroupArr[$groupConfig->name] = $groupConfig->type === 'images'
                    ? json_decode($groupConfig->value, true)
                    : $groupConfig->value;
            }
            $groupConfigs = $regroupArr;
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
