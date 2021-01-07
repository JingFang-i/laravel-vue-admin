<?php

namespace Jmhc\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Jmhc\Admin\Commands\ImportSql;
use Jmhc\Admin\Commands\ResetAdmin;
use Jmhc\Admin\Commands\ServiceCommand;
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
        'fatalError',
    ];

    /**
     * 在注册后启动服务
     *
     * @return void
     */
    public function boot(ResponseFactory $responseFactory, ApiResponseInterface $apiResponse)
    {
        // 注册宏命令
        $this->registerDataResponseMacro($responseFactory, $apiResponse);
        $this->registerMsgResponseMacro($responseFactory, $apiResponse);
//        // 加载语言
//        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'frey');
        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/../routes/route.php');

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
            __DIR__ . '/../config/admin.php.stub' => config_path('admin.php'),
            __DIR__ . '/../config/permission.php.stub' => config_path('permission.php'),
            __DIR__ . '/../routes/RouteServiceProvider.php.stub' => app_path('Providers/RouteServiceProvider.php'),
            __DIR__ . '/../routes/admin.php' => base_path('routes/admin.php'),
            __DIR__ . '/../resources/page' => resource_path('page'),
            __DIR__ . '/../resources/build' => base_path('build'),
            __DIR__ . '/../resources/plop-templates' => resource_path('plop-templates'),
            __DIR__ . '/../resources/configs' => base_path(),
            __DIR__ . '/../resources/index.html' => resource_path('index.html'),
            __DIR__ . '/../exception/Handler.php.stub' => app_path('Exceptions/Handler.php'),
            __DIR__ . '/UEditor/config.json' => resource_path('ueditor/config.json'),
            __DIR__ . '/../resources/plugins' => public_path('plugins'),
        ], 'freyadmin');
    }

    /**
     * 注册命令
     */
    protected function registerCommand()
    {
        $this->commands([
            ServiceCommand::class,
            ImportSql::class,
            ResetAdmin::class,
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
        $responseFactory->macro('success', function($data = [], string $msg = '', int $code = 200) use ($responseFactory, $apiResponse){
            return $responseFactory->make($apiResponse->success($data, $msg, $code));
        });
        //error
        $responseFactory->macro('error', function(string $msg = '', int $code = 400, array $data = []) use ($responseFactory, $apiResponse){
            return $responseFactory->make($apiResponse->error($msg, $code, $data));
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
