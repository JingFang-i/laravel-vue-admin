<?php


namespace Jmhc\Admin\Middleware;

use Closure;
use Illuminate\Routing\ResponseFactory;

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
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        $name = $request->route()->getName();
        if (empty($name) || !$user->hasPermissionTo($name)) {
            return response()->unauthenticated();
        }
        return $next($request);
    }
}
