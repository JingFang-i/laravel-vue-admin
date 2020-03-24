<?php


namespace Jmhc\Admin\Factories;

use Illuminate\Support\Facades\Route;
use Jmhc\Admin\Contracts\Repository;
use Jmhc\Admin\Contracts\Service;

class ServiceBindFactory
{
    protected $app = null;

    protected $serviceName; //服务名称，可以包含路径 以 "\" 连接

    protected $modelClassName = ''; //模型类名
    protected $repositoryClassName = ''; // 仓储类类名
    protected $serviceClassName = ''; //服务类名

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
        $namespace = Route::current()->getAction('namespace');
        if ($namespace === "Jmhc\\Admin\\Controllers") {
            config([
                'admin.model_prefix' => "Jmhc\\Admin\\Models",
                'admin.repository_prefix' => "Jmhc\\Admin\\Repositories",
                'admin.service_prefix' => "Jmhc\\Admin\\Services",
            ]);
        }
        $this->buildClassName();
    }

    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

    public function getApp()
    {
        if (is_null($this->app)) {
            $this->app = app();
        }
        return $this->app;
    }

    /**
     * 绑定一个服务到容器
     */
    public function bind($isBind = true): void
    {
        $app = $this->getApp();

        //绑定到接口实现
        if ($isBind) {
            $app->bind(Repository::class, $this->repositoryClassName);
            $app->bind(Service::class, $this->serviceClassName);
        }

        //绑定仓储到容器
        $app->singleton($this->repositoryClassName, function ($app) {
            return new $this->repositoryClassName($app->make($this->modelClassName));
        });
        //绑定服务
        $app->singleton($this->serviceClassName, function ($app) {
            return new $this->serviceClassName($app->make($this->repositoryClassName));
        });
    }

    /**
     * 获取服务实例
     * @return Service
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getService($isCurrent = true): Service
    {
        $app = $this->getApp();
        $containerKey = $isCurrent ? Service::class : $this->serviceClassName;
        if (!$app->has($containerKey)) {
            $this->bind($isCurrent);
        }
        return $app->make($containerKey);
    }

    /**
     * 获取仓储实例
     * @param bool $isCurrent
     * @return Repository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getRepository($isCurrent = true): Repository
    {
        $app = $this->getApp();
        $containerKey = $isCurrent ? Repository::class : $this->repositoryClassName;
        if (!$app->has($containerKey)) {
            $this->bind($isCurrent);
        }
        return $app->make($containerKey);
    }

    /**
     * 生成模型、仓储、服务、转换器、验证器类名
     */
    private function buildClassName(): void
    {
        $this->modelClassName = config('admin.model_prefix') .
            "\\" . $this->serviceName;
        $this->repositoryClassName = config('admin.repository_prefix')
            . "\\" . $this->serviceName . 'Repository';
        $this->serviceClassName = config('admin.service_prefix')
            . "\\" . $this->serviceName . 'Service';
    }

}
