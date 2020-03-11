<?php

namespace Jmhc\Admin\Providers;

use Jmhc\Admin\Api\ApiResponse;
use Jmhc\Admin\Contracts\ApiResponseInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use League\Fractal\TransformerAbstract;

class ApiResponseServiceProvider extends ServiceProvider
{

    protected $dataMacro = [
        'paginator',
        'collection',
        'item',
    ];

    protected $msgMacro = [
        'paramError',
        'queryNull',
        'unauthenticated',
        'tokenInvalid',
    ];

    public function register()
    {
        $this->app->singleton(ApiResponseInterface::class, function($app) {
            return new ApiResponse();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(ResponseFactory $responseFactory, ApiResponseInterface $apiResponse)
    {
        $this->registerDataResponseMacro($responseFactory, $apiResponse);
        $this->registerMsgResponseMacro($responseFactory, $apiResponse);
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
     * 定义普通信息响应宏
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
