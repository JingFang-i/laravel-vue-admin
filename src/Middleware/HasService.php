<?php

namespace Jmhc\Admin\Middleware;

use Illuminate\Support\Facades\Route;
use Jmhc\Admin\Factories\ServiceBindFactory;
use Closure;

class HasService
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::current()->getAction();
        $controllerClass = explode('@', $route['controller'])[0];

        $name = str_replace([$route['namespace'] . "\\", 'Controller'], '', $controllerClass);
        (new ServiceBindFactory($name))->bind();
        return $next($request);
    }
}
