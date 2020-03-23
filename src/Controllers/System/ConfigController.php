<?php


namespace Jmhc\Admin\Controllers\System;


use Illuminate\Routing\Controller;
use Jmhc\Admin\Services\System\DictionaryService;
use Jmhc\Admin\Contracts\Service;
use Jmhc\Admin\Traits\HasResourceRoutes;

class ConfigController extends Controller
{
    use HasResourceRoutes;

    /**
     * 更新一个组的值
     */
    public function updateGroup(Service $service)
    {
        return $service->updateGroup();
    }

    /**
     * 获取配置组
     * @return mixed
     */
    public function getConfigGroup()
    {
        return DictionaryService::instance()->getDict('config_group');
    }

    /**
     * 获取站点配置
     */
    public function getWebsiteConfig(Service $service)
    {
        return $service->getWebsiteConfig();
    }
}
