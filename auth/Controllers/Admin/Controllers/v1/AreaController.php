<?php


namespace App\Http\Admin\Controllers\v1;


use App\Http\Admin\Controllers\Controller;
use App\Library\Service;

class AreaController extends Controller
{

    public function index(Service $service)
    {
        return $service->index();
    }
}
