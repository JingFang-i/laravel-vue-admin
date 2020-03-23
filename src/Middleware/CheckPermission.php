<?php


namespace Jmhc\Admin\Middleware;

use Closure;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPermission
{

    protected $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard)
    {
        $user = auth($guard)->user();
        if ($user->hasRole(1)) {
            return $next($request);
        }
        if (!$user->hasPermissionTo($request->route()->getName())) {
            return $this->response->unauthenticated();
        }
        return $next($request);
    }
}
