<?php


namespace App\Http\Admin\Controllers\v1;


use App\Http\Admin\Controllers\Controller;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\UploadService;

class CommonController extends Controller
{
    /**
     * 上传
     * @param Attachment $attachment
     * @return mixed
     */
    public function upload(Attachment $attachment)
    {
        $uploadService = new UploadService(new AttachmentRepository($attachment));
        return $uploadService->upload();
    }


}
