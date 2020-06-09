<?php


namespace Jmhc\Admin\Traits;


trait HasError
{
    protected $errorMsg = '发生错误';

    public function setError(string $msg)
    {
        $this->errorMsg = $msg;
    }

    public function getError(): string
    {
        return $this->errorMsg;
    }
}
