<?php


namespace Jmhc\Admin\Services;

use Jmhc\Admin\Service;
use Illuminate\Http\UploadedFile;

class UploadService extends Service
{

    /**
     * 上传文件
     * @param integer $userType 上传人类型1=平台管理员,2=商家
     * @param integer $adminId 上传人id
     * @return mixed
     */
    public function upload()
    {
        if (!$this->request->hasFile('image')) {
            return $this->response->error('文件不存在!');
        }
        $files = $this->request->file('image');
        $responseData = [];
        if (is_array($files) && count($files) > 0) {
            foreach ($files as $file) {
                if ($res = $this->validateFile($file) !== true) {
                    return $this->response->error($res);
                }
                $responseData[] = $this->storeFile($file);
            }
        } else {
            if ($res = $this->validateFile($files) !== true) {
                return $this->response->error($res);
            }
            $responseData = $this->storeFile($files);
        }
        if (empty($responseData)) {
            return $this->response->error('上传失败');
        }
        return $this->response->success($responseData);

    }

    /**
     * 存储保存文件
     *
     * @param $file
     * @param $userType
     * @param $adminId
     * @param $albumId
     * @return array
     */
    protected function storeFile(UploadedFile $file)
    {
        $filename = $file->store(config('upload.path'));
        if ($filename) {
            $this->saveFileInfo($file, $filename);
            return [
                'url' => asset('storage/' . $filename),
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
    protected function saveFileInfo(UploadedFile $file, $filename)
    {
        $data = [
            'name' => $file->getClientOriginalName(),
            'user_id' => auth('api')->id(),
            'path' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];

        return $this->repository->store($data);
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
