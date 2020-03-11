<?php


namespace Jmhc\Admin\Providers;


use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * 在注册后启动服务
     *
     * @return void
     */
    public function boot()
    {
        $this->publishResources();
    }


    public function publishResources()
    {
        $this->publishes([
            __DIR__ . '/../../config/serviceloader.php' => config_path('serviceloader.php'),
            __DIR__ . '/../../config/upload.php' => config_path('upload.php'),
            __DIR__ . '/../../resources/page' => resource_path('page'),
            __DIR__ . '/../../resources/plop-templates' => resource_path('plop-templates'),
            __DIR__ . '/../../resources/configs' => base_path(),
            __DIR__ . '/../../routes/admin.php' => base_path('routes/admin.php'),
            __DIR__ . '/../../routes/api.php' => base_path('routes/api.php'),
        ]);
    }
}