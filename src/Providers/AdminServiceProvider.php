<?php

namespace Jmhc\Admin\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Jmhc\Admin\Console\Commands\ServiceCommand;
use Illuminate\Support\ServiceProvider;
use Jmhc\Admin\Contracts\ApiResponseInterface;
use Jmhc\Admin\Response\ApiResponse;
use League\Fractal\TransformerAbstract;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * 返回数据的响应
     * @var array
     */
    protected $dataMacro = [
        'paginator',
        'collection',
        'item',
    ];

    /**
     * 返回错误信息
     * @var array
     */
    protected $msgMacro = [
        'paramError',
        'queryNull',
        'unauthenticated',
        'tokenInvalid',
    ];

    /**
     * 在注册后启动服务
     *
     * @return void
     */
    public function boot(ResponseFactory $responseFactory, ApiResponseInterface $apiResponse)
    {
        $this->publishResources(); // 发布静态资源
        $this->registerCommand(); // 注册命令
        $this->loadRoutes(); //加载路由
        //注册宏命令
        $this->registerDataResponseMacro($responseFactory, $apiResponse);
        $this->registerMsgResponseMacro($responseFactory, $apiResponse);

    }

    /**
     * 注册
     */
    public function register()
    {
        $this->app->singleton(ApiResponseInterface::class, function($app) {
            return new ApiResponse();
        });
    }

    /**
     * 发布静态资源
     */
    protected function publishResources()
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

    /**
     * 注册命令
     */
    protected function registerCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ServiceCommand::class,
            ]);
        }
    }


    /**
     * 定义数据查询响应宏
     *
     * @param $responseFactory
     * @param $apiResponse
     */
    protected function registerDataResponseMacro($responseFactory, $apiResponse)
    {
        foreach ($this->dataMacro as $macro) {
            $responseFactory->macro($macro, function ($resource,
                                                      TransformerAbstract $transformer = null)
            use ($responseFactory, $apiResponse, $macro){
                return $responseFactory->make($apiResponse->$macro($resource, $transformer));
            });
        }
    }

    /**
     * 定义错误信息响应宏
     *
     * @param $responseFactory
     * @param $apiResponse
     */
    protected function registerMsgResponseMacro($responseFactory, $apiResponse)
    {
        //success
        $responseFactory->macro('success', function($data = [], string $msg = '') use ($responseFactory, $apiResponse){
            return $responseFactory->make($apiResponse->success($data, $msg));
        });
        //error
        $responseFactory->macro('error', function(string $msg = '', int $code = 5000) use ($responseFactory, $apiResponse){
            return $responseFactory->make($apiResponse->error($msg, $code));
        });
        //other
        foreach ($this->msgMacro as $macro) {
            $responseFactory->macro($macro, function (string $msg = '', array $bindings = [])
            use ($responseFactory, $apiResponse, $macro){
                return $responseFactory->make($apiResponse->$macro($msg, $bindings));
            });
        }
    }

    /**
     * 加载路由
     */
    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/admin.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }


}