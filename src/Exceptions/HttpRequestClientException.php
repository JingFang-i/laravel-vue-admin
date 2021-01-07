<?php


namespace Jmhc\Admin\Exceptions;

use Exception;

class HttpRequestClientException extends Exception
{
    /**
     * 报告异常
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * 转换异常为 HTTP 响应
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->fatalError($this->getMessage());
    }

}