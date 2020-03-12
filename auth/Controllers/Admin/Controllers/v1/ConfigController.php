<?php


namespace App\Http\Admin\Controllers\v1;


use App\Http\Admin\Controllers\Controller;
use App\Library\Service;

class ConfigController extends Controller
{
    public function index(Service $service)
    {
        return $service->index();
    }
}