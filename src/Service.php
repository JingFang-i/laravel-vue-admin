<?php


namespace Jmhc\Admin;

use Jmhc\Admin\Factories\ServiceBindFactory;
use Jmhc\Admin\Traits\HasResourceActions;
use Jmhc\Admin\Traits\HasValidate;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Validator;
use League\Fractal\TransformerAbstract;

abstract class Service implements \Jmhc\Admin\Contracts\Service
{
    use HasResourceActions, HasValidate;

    protected $repository; //仓储实例，主要用于数据处理
    protected $validator; //表单验证请求
    protected $transformer; //数据转换器

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request; // 请求实例
    /**
     *
     * @var ResponseFactory
     */
    protected $response; // 响应实例

    protected $user; //当前登录的用户
    protected $guardName; //当前守卫名称

    protected $errorMsg = ''; // 错误信息

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->request = request();
        $this->response = resolve('Illuminate\Contracts\Routing\ResponseFactory');

        $routePrefix = $this->request->route()->getPrefix();
        if (strpos($routePrefix, 'admin') !== false) {
            $this->guardName = 'admin';
            $this->user = auth('admin')->user();
        } else {
            $this->guardName = 'api';
            $this->user = auth('api')->user();
        }

    }

    /**
     * 设置转换实例
     * @param TransformerAbstract $transformer
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * 设置验证请求实例
     * @param Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->errorMsg;
    }

    /**
     * 获取错误信息
     * @param string $errMsg
     */
    public function setError(string $errMsg)
    {
        $this->errorMsg = $errMsg;
    }

    /**
     * 获取服务实例
     * @return \Jmhc\Admin\Contracts\Service
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function instance()
    {
        $modelName = str_replace(config('serviceloader.service_prefix') . "\\", '', static::class);
        $modelName = substr($modelName, 0, -7); //去掉Service后缀
        return (new ServiceBindFactory($modelName))->getService(false);
    }

    /**
     * @param $method
     * @param $arg
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function __callStatic($method, $arg)
    {
        return static::instance()->$method(...$arg);
    }

}
