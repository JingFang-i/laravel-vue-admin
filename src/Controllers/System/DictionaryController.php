<?php


namespace Jmhc\Admin\Controllers\System;


use Illuminate\Routing\Controller;
use Jmhc\Admin\Contracts\Service;
use Jmhc\Admin\Traits\HasResourceRoutes;

class DictionaryController extends Controller
{
    use HasResourceRoutes;

    /**
     * 获取字典值
     * @param Service $service
     * @return mixed
     */
    public function getDict(Service $service)
    {
        $name = request()->input('name');
        return $service->getDict($name);
    }
}
