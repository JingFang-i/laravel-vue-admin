<?php

namespace Jmhc\Admin\Middleware;

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
        $controllerNamespace = explode('@', $request->route()->getActionName())[0];
        $classNameArr = explode("\\", $controllerNamespace);//控制器类名
        array_splice($classNameArr, 0, 5); //去除前缀

        $name = str_replace('Controller', '', join("\\", $classNameArr));
        (new ServiceBindFactory($name))->bind();
        return $next($request);
    }
}
