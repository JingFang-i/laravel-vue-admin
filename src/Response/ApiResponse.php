<?php


namespace Jmhc\Admin\Response;

use Illuminate\Support\Collection;
use Jmhc\Admin\Contracts\ApiResponseInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class ApiResponse implements ApiResponseInterface
{

    protected $transformer;

    public function __construct()
    {
        $this->transformer = new Transformer();
    }

    /**
     * 分页资源响应
     *
     * @return Result
     */
    public function paginator(Paginator $paginator, TransformerAbstract $transformer = null)
    {
        return $this->success($this->transformer->paginator($paginator, $transformer));
    }

    /**
     * 资源集合响应
     *
     * @return Result
     */
    public function collection(Collection $collection, TransformerAbstract $transformer = null)
    {
        return $this->success($this->transformer->collection($collection, $transformer));
    }

    /**
     * 单一资源响应
     *
     * @return Result
     */
    public function item(Model $model, TransformerAbstract $transformer = null)
    {
        return $this->success($this->transformer->item($model, $transformer));
    }

    /**
     * 成功响应
     *
     * @return Result
     */
    public function success(array $data = [], string $msg = '', int $code = 200)
    {
        return new Result($code, $data, $msg);
    }

    /**
     * 错误响应
     *
     * @return Result
     */
    public function error(string $msg = '', int $code = 400, array $data = [])
    {
        return new Result($code, $data, $msg);
    }

    /**
     * 未认证的
     *
     * @return Result
     */
    public function unauthenticated()
    {
        return new Result(401);
    }

    /**
     * 参数错误
     *
     * @param string $msg
     * @param array $data
     * @return Result
     */
    public function paramError(string $msg, array $data = [])
    {
        return new Result(400, $data, $msg);
    }

    /**
     * 空数据
     *
     * @return Result
     */
    public function queryNull()
    {
        return new Result(204, null, '未查询到任何结果');
    }

    /**
     * token 失效
     * @return Result
     */
    public function tokenInvalid()
    {
        return new Result(420);
    }

    /**
     * 致命错误
     * @return Result
     */
    public function fatalError(string $msg)
    {
        return new Result(500, [], $msg);
    }

}
