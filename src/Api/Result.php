<?php


namespace Jmhc\Admin\Api;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Result implements Jsonable, Arrayable
{
    protected $codeStatus; //返回的code
    protected $message; //返回的错误或提示信息
    protected $data; //返回的数据

    protected $code = [
        2000 => '请求成功',
        2001 => '请求成功,无数据',
        3000 => '接口维护中',
        4000 => '请求失败',
        4001 => '账号在其他地方登录',
        4002 => '未登录',
        4003 => 'token无效',
        4004 => '禁止登录',
        4005 => '短信发送间隔时间未到',
        5000 => '发生异常',
        5001 => '发生错误',
        6000 => '版本过低',
    ];

    public function __construct($codeStatus = 2000, $data = [], $message = '')
    {
        $this->data = $data;
        $this->codeStatus = $codeStatus;
        if (!$message) {
            $this->message = array_key_exists($codeStatus, $this->code) ? $this->code[$codeStatus] : null;
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
        ]);
    }
}
