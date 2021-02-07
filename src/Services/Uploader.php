<?php


namespace Jmhc\Admin\Services;


use Jmhc\Admin\Models\System\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Jmhc\Admin\Traits\HasError;

class Uploader
{

    use HasError;

    protected $config = [];
    protected $disk;


    public function __construct(array $config = [], string $disk = 'public')
    {
        $this->disk = $disk;
        $this->config = config('admin.upload');
        $this->config = array_merge($this->config, $config);
    }

    public function upload($files)
    {
        if (!$files) {
            $this->setError('请上传文件');
            return false;
        }
        $responseData = [];
        if (is_array($files) && count($files) > 0) {
            foreach ($files as $file) {
                $res = $this->validateFile($file);
                if ($res !== true) {
                    $this->setError($res);
                    return false;
                }
                $responseData[] = $this->storeFile($file);
            }
        } else {
            $res = $this->validateFile($files);
            if ($res !== true) {
                $this->setError($res);
                return false;
            }
            $responseData = $this->storeFile($files);
        }
        if (empty($responseData)) {
            $this->setError('上传失败');
            return false;
        }
        return $responseData;
    }

    /**
     * 存储保存文件
     *
     * @param $file
     * @param $userType
     * @param $adminId
     * @return array
     */
    protected function storeFile(UploadedFile $file)
    {
        $filename = Storage::disk($this->disk)->putFile($this->config['path'], $file);
        if ($filename) {
            if ($this->disk === 'public') {
                $filename = '/storage/' . $filename;
                $url = asset($filename);
            } else {
                $url = '';
            }
            if (!isset($this->config['save_to_table']) || $this->config['save_to_table'] === true) {
                $this->saveFileInfo($file, $filename);
            }
            return [
                'url' => $url,
                'filename' => $filename,
                'name' => $file->getClientOriginalName(),
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
            return $file->getErrorMessage();
        }

        if (!$this->validateExt($file->getClientOriginalExtension())) {
            return '文件格式错误';
        }

        if (!$this->validateSize($file->getSize())) {
            return '文件大小超限';
        }
        return true;
    }

    /**
     * 验证扩展名
     *
     * @param $ext
     * @return bool
     */
    protected function validateExt($ext)
    {
        return in_array($ext, $this->config['ext']);
    }

    /**
     * 验证大小
     *
     * @param $size
     * @return bool
     */
    protected function validateSize($size)
    {
        $configSize = $this->config['size'];
        return $size / 1024 / 1024 < intval($configSize);
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
            'album_id' => intval(request()->input('album_id')),
            'name' => $file->getClientOriginalName(),
            'admin_id' => auth('admin')->id(),
            'path' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];

        return Attachment::create($data);
    }
}