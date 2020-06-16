<?php


namespace Jmhc\Admin\Services;


use Jmhc\Admin\Service;

class TestService extends Service
{
    public function index()
    {
        return 'Test is successful.';
    }
}