<?php


namespace App\Services;

use Jmhc\Admin\Service;
use Illuminate\Support\Facades\Cache;

class AreaService extends Service
{
    public function index()
    {
        $pid = $this->request->input('pid', 0);

        $key = 'area_pid' . $pid;
        if (Cache::has($key)) {
            $areas = Cache::get($key);
        } else {
            $areas = $this->repository->where('pid', intval($pid))
                ->select('id', 'name', 'level', 'first')
                ->get();
            Cache::put($key, $areas);
        }
        if ($areas) {
            return $this->response->collection($areas);
        } else {
            return $this->response->queryNull();
        }
    }
}
