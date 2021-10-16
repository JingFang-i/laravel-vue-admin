<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Log::error('systemError', [$e->getMessage(), $e->getTraceAsString()]);
        });

        $this->renderable(function (AuthenticationException $e, $request){
            return $request->ajax() ? response()->tokenInvalid() : abort(404); //未认证
        });
        $this->renderable(function (TokenExpiredException $e, $request){
            return $request->ajax() ? response()->tokenInvalid() : abort(404); //未认证
        });
        $this->renderable(function (TokenBlacklistedException $e, $request){
            return $request->ajax() ? response()->error('禁止登录', 403) : abort(403); //未认证
        });

        $this->renderable(function (Throwable $e) {
            return response()->error('系统异常');
        });

    }
}
