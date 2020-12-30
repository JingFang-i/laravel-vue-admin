<?php


namespace Jmhc\Admin\Response;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Result implements Jsonable, Arrayable
{
    protected $codeStatus; //返回的code
    protected $message; //返回的错误或提示信息
    protected $data; //返回的数据

    protected $code = [
        200 => '请求成功',
        204 => '请求成功,无数据',
        400 => '错误请求',
        401 => '无权限',
        403 => '禁止访问',
        404 => '未找到',
        405 => '无效的请求',
        420 => '身份凭证已失效',
        500 => '服务器发生异常',
        503 => '服务器维护中',
    ];

    public function __construct($codeStatus = 200, $data = [], $message = '')
    {
        $this->data = $data;
        $this->codeStatus = $codeStatus;
        if (!$message) {
            $this->message = isset($this->code[$codeStatus]) ? $this->code[$codeStatus] : null;
        } else {
            $this->message = $message;
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'code' => $this->codeStatus,
            'msg' => $this->message,
            'data' => $this->data,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode([
            'code' => $this->codeStatus,
            'msg' => $this->message,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);
    }
}
