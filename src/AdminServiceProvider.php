<?php

namespace Jmhc\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Jmhc\Admin\Console\Commands\ImportSql;
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
        //注册宏命令
        $this->registerDataResponseMacro($responseFactory, $apiResponse);
        $this->registerMsgResponseMacro($responseFactory, $apiResponse);

        if ($this->app->runningInConsole()) {
            $this->publishResources(); // 发布静态资源
            $this->registerCommand(); // 注册命令
        }
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
            __DIR__ . '/../config/serviceloader.php.stub' => config_path('serviceloader.php'),
            __DIR__ . '/../config/upload.php.stub' => config_path('upload.php'),
            __DIR__ . '/../config/permission.php.stub' => config_path('permission.php'),
            __DIR__ . '/../resources/page' => resource_path('page'),
            __DIR__ . '/../resources/plop-templates' => resource_path('plop-templates'),
            __DIR__ . '/../resources/configs' => base_path(),
            __DIR__ . '/../routes/admin.php' => base_path('routes/admin.php'),
            __DIR__ . '/../routes/api.php' => base_path('routes/api.php'),
            __DIR__ . '/RouteServiceProvider.php' => app_path('Providers/RouteServiceProvider.php'),
            __DIR__ . '/../auth/Controllers' => app_path('Http'),
            __DIR__ . '/../auth/Models' => app_path('Models'),
            __DIR__ . '/../auth/Repositories' => app_path('Repositories'),
            __DIR__ . '/../auth/Services' => app_path('Services'),
        ]);
    }

    /**
     * 注册命令
     */
    protected function registerCommand()
    {
        $this->commands([
            ServiceCommand::class,
            ImportSql::class,
        ]);
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


}