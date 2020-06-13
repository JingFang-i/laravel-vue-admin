<?php


namespace Jmhc\Admin;


use Jmhc\Admin\Factories\ServiceBindFactory;
use Jmhc\Admin\Traits\HasMultiDestroy;
use Jmhc\Admin\Traits\HasMultiEdit;
use Jmhc\Admin\Traits\HasResourceActions;
use Jmhc\Admin\Traits\HasValidate;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Validator;
use League\Fractal\TransformerAbstract;
use Jmhc\Admin\Contracts\Service as ServiceInterface;

abstract class Service implements ServiceInterface
{
    use HasResourceActions, HasValidate, HasMultiEdit, HasMultiDestroy;

    /**
     * @var Repository|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected $repository; //仓储实例，主要用于数据处理
    protected $validator; //表单验证请求
    protected $transformer; //数据转换器

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request; // 请求实例
    /**
     * @var ResponseFactory
     */
    protected $response; // 响应实例

    protected $user = null; // 当前登录的用户
    protected $guardName = ''; // 当前守卫名称

    protected $errorMsg = ''; // 错误信息

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->request = request();
        $this->response = response();

    }

    /**
     * 设置转换实例
     * @param TransformerAbstract $transformer
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * 设置验证请求实例
     * @param Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
        return $this;
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
     * @return \Jmhc\Admin\Contracts\Service|static:class
     * @throws \Exception
     */
    public static function instance()
    {
        $serviceName = explode("\\Services\\", static::class)[1];

        $modelName = substr($serviceName, 0, -7); //去掉Service后缀
        return (new ServiceBindFactory($modelName))->getService(false);
    }

    public function guardName()
    {
        if (!$this->guardName) {
            $this->guardName = auth()->getDefaultDriver();
        }
        return $this->guardName;
    }

    /**
     * Get the currently authenticated user.
     */
    public function user()
    {
        if (is_null($this->user)) {
            $this->user = auth()->user();
        }
        return $this->user;
    }


}
