<?php


namespace Jmhc\Admin\Contracts;


use Jmhc\Admin\Response\Result;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

interface ApiResponseInterface
{
    /**
     * 分页资源响应
     *
     * @return Result
     */
    public function paginator(Paginator $paginator, TransformerAbstract $transformer);

    /**
     * 资源集合响应
     *
     * @return Result
     */
    public function collection(Collection $collection, TransformerAbstract $transformer);

    /**
     * 单一资源响应
     *
     * @return Result
     */
    public function item(Model $model, TransformerAbstract $transformer);

    /**
     * 成功响应
     *
     * @return Result
     */
    public function success(array $data);

    /**
     * 错误响应
     *
     * @return Result
     */
    public function error(string $msg, int $code);
}
