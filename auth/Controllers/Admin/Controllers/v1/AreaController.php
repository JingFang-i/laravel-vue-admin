<?php


namespace App\Http\Admin\Controllers\v1;


use App\Http\Admin\Controllers\Controller;
use Jmhc\Admin\Contracts\Service;

class AreaController extends Controller
{

    public function index(Service $service)
    {
        return $service->index();
    }
}
