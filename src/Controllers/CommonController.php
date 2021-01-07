<?php


namespace Jmhc\Admin\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Jmhc\Admin\Services\Uploader;
use Jmhc\Admin\UEditor\UEditor;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Token;

class CommonController extends Controller
{
    /**
     * 上传
     * @return mixed
     */
    public function upload(ResponseFactory $response)
    {
        $file = request()->file('file');
        $uploader = new Uploader();
        $fileInfo = $uploader->upload($file);
        if ($fileInfo === false) {
            return $response->error($uploader->getError());
        }
        return $response->success($fileInfo);
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
