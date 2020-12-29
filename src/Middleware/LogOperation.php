<?php


namespace Jmhc\Admin\Middleware;


use Closure;
use Illuminate\Support\Facades\Route;
use Jmhc\Admin\Models\System\AdminLog;
use Spatie\Permission\Models\Permission;

class LogOperation
{

    protected $model;

    public function __construct(AdminLog $adminLog)
    {
        $this->model = $adminLog;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('admin.allow_write_admin_log')) {
            return $next($request);
        }
        $user = auth()->user();
        if ($user) {
            $permission = Permission::where('name', Route::currentRouteName())->select('pid', 'title')->first();
            if ($permission) {
                if ($permission->pid) {
                    $parentTitle = Permission::where('id', $permission->pid)->value('title');
                    $permission->title = $parentTitle . '->' . $permission->title;
                }

                $this->model->admin_id = $user->id;
                $this->model->name = $user->name;
                $this->model->title = $permission->title ?? '';
                $this->model->ip = ip2long($request->ip());
                $this->model->content = [
                    'url' => $request->url(),
                    'name' => $request->route()->getName(),
                    'params' => $request->query(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent(),
                ];
                $this->model->save();
            }
        }
        return $next($request);
    }
}
