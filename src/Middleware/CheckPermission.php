<?php


namespace Jmhc\Admin\Middleware;

use Closure;
use Illuminate\Routing\ResponseFactory;
use Jmhc\Admin\UserGuard;

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
        UserGuard::setGuard($guard);
        $user = UserGuard::getUser();
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        if (!$user->hasPermissionTo($request->route()->getName())) {
            return $this->response->unauthenticated();
        }
        return $next($request);
    }
}
