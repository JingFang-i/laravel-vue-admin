<?php


namespace Jmhc\Admin\Controllers;


use Illuminate\Routing\Controller;
use Jmhc\Admin\UEditor\UEditor;
use Jmhc\Admin\Services\UploadService;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Token;

class CommonController extends Controller
{
    /**
     * 上传
     * @return mixed
     */
    public function upload()
    {
        return (new UploadService())->upload();
    }

    /**
     * ueditor编辑器服务
     * @param UEditor $editor
     * @return mixed
     */
    public function ueditor(UEditor $editor)
    {
        try {
            return response($editor->handle(), 200)
                ->header('Content-Type', 'text/javascript');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

}
