<?php


namespace Jmhc\Admin\Services;

use Jmhc\Admin\Models\System\Attachment;
use Illuminate\Http\UploadedFile;

class UploadService
{

    /**
     * 上传文件
     * @param integer $userType 上传人类型1=平台管理员,2=商家
     * @param integer $adminId 上传人id
     * @return mixed
     */
    public function upload()
    {
        $request = request();
        $response = response();
        if (!$request->hasFile('file')) {
            return $response->error('文件不存在!');
        }
        $files = $request->file('file');
        $albumId = $request->input('album_id', 0);
        $responseData = [];
        if (is_array($files) && count($files) > 0) {
            foreach ($files as $file) {
                if ($res = $this->validateFile($file) !== true) {
                    return $response->error($res);
                }
                $responseData[] = $this->storeFile($file, $albumId);
            }
        } else {
            if ($res = $this->validateFile($files) !== true) {
                return $response->error($res);
            }
            $responseData = $this->storeFile($files, $albumId);
        }
        if (empty($responseData)) {
            return $response->error('上传失败');
        }
        return $response->success($responseData);

    }

    /**
     * 存储保存文件
     *
     * @param $file
     * @param $userType
     * @param $adminId
     * @return array
     */
    protected function storeFile(UploadedFile $file, $albumId)
    {
        $filename = $file->store(config('upload.path'));
        if ($filename) {
            $filename = 'storage/' . $filename;
            $this->saveFileInfo($file, $filename, $albumId);
            return [
                'url' => asset($filename),
                'filename' => $filename,
            ];
        } else {
            return [];
        }
    }

    /**
     * 文件验证
     *
     * @param UploadedFile $file
     * @return bool|string
     */
    protected function validateFile(UploadedFile $file)
    {
        if (!$file->isValid()) {
            return $file->getClientOriginalName() . '上传文件无效!';
        }

        if (!$this->validateExt($file->extension())) {
            return $file->getClientOriginalName() . '上传文件格式不正确!';
        }

        if (!$this->validateSize($file->getSize())) {
            return $file->getClientOriginalName() . '上传文件大小超过限制';
        }
        return true;
    }

    /**
     * 保存附件信息
     *
     * @param UploadedFile $file
     * @param $filename
     * @param $userType
     * @param $adminId
     * @return mixed
     */
    protected function saveFileInfo(UploadedFile $file, $filename, $albumId)
    {
        $data = [
            'album_id' => intval($albumId),
            'name' => $file->getClientOriginalName(),
            'admin_id' => auth()->id(),
            'path' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];

        return Attachment::instance()->store($data);
    }

    /**
     * 验证扩展名
     *
     * @param $ext
     * @return bool
     */
    protected function validateExt($ext)
    {
        return in_array($ext, config('upload.ext'));
    }

    /**
     * 验证大小
     *
     * @param $size
     * @return bool
     */
    protected function validateSize($size)
    {
        $configSize = config('upload.size');
        return $size / 1024 / 1024 < intval($configSize);
    }
}
