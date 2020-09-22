<?php


namespace Jmhc\Admin\UEditor;


use Jmhc\Admin\Models\System\Attachment;
use Illuminate\Http\Request;

class UEditor
{
    protected $config = [];
    protected $request;

    public function __construct(Request $request)
    {
        date_default_timezone_set("Asia/Shanghai");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");
        $configPath = __DIR__ . '/config.json';
        if(file_exists(resource_path('ueditor/config.json'))) {
            $configPath = resource_path('ueditor/config.json');
        }
        $this->config = json_decode(
            preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($configPath)
            ), true);
        $this->request = $request;
    }

    public function handle()
    {
        if (!$this->request->has('action')) {
            return json_encode([
                'state'=> '缺少参数'
            ]);
        }
        $action = $this->request->query('action');
        $result = $this->getResult($action);
        $result = json_encode($result);

        /* 输出结果 */
        if ($this->request->has('callback')) {
            $callback = $this->request->query('callback');
            if (preg_match("/^[\w_]+$/", $callback)) {
                return htmlspecialchars($callback) . '(' . $result . ')';
            } else {
                return json_encode([
                    'state'=> 'callback参数不合法'
                ]);
            }
        } else {
            return $result;
        }
    }

    /**
     * 获取处理结果
     * @param string $action
     * @return array
     */
    protected function getResult(string $action): array
    {
        switch ($action) {
            case 'config':
                $result =  $this->config;
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = $this->actionUpload($action);
                $this->storeFileInfo($result);
                $result['url'] = asset($result['url']);
                break;

            /* 列出图片 */
            case 'listimage':
                $result = $this->actionList($action);
                break;
            /* 列出文件 */
            case 'listfile':
                $result = $this->actionList($action);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->actionCrawler();
                break;

            default:
                $result = [
                    'state'=> '请求地址出错'
                ];
                break;
        }
        return $result;
    }

    /**
     * 文件上传
     * @param $action
     * @return array
     */
    protected function actionUpload(string $action): array
    {
        switch ($action) {
            /* 上传图片 */
            case 'uploadimage':
                $config = [
                    "pathFormat" => $this->config['imagePathFormat'],
                    "maxSize" => $this->config['imageMaxSize'],
                    "allowFiles" => $this->config['imageAllowFiles']
                ];
                $fieldName = $this->config['imageFieldName'];
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $config = [
                    "pathFormat" => $this->config['scrawlPathFormat'],
                    "maxSize" => $this->config['scrawlMaxSize'],
                    "allowFiles" => $this->config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                ];
                $fieldName = $this->config['scrawlFieldName'];
                $base64 = "base64";
                break;
            /* 上传视频 */
            case 'uploadvideo':
                $config = [
                    "pathFormat" => $this->config['videoPathFormat'],
                    "maxSize" => $this->config['videoMaxSize'],
                    "allowFiles" => $this->config['videoAllowFiles']
                ];
                $fieldName = $this->config['videoFieldName'];
                break;
            /* 上传文件 */
            case 'uploadfile':
            default:
                $config = [
                    "pathFormat" => $this->config['filePathFormat'],
                    "maxSize" => $this->config['fileMaxSize'],
                    "allowFiles" => $this->config['fileAllowFiles']
                ];
                $fieldName = $this->config['fileFieldName'];
        }
        $uploader = new Uploader($fieldName, $config, $base64);
        return $uploader->getFileInfo();
    }

    /**
     * 获取已上传的文件列表
     * Date: 14-04-09
     * Time: 上午10:17
     */
    protected function actionList(string $action): array
    {
        /* 判断类型 */
        switch ($action) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $this->config['fileManagerAllowFiles'];
                $listSize = $this->config['fileManagerListSize'];
                $path = $this->config['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $this->config['imageManagerAllowFiles'];
                $listSize = $this->config['imageManagerListSize'];
                $path = $this->config['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = $this->request->has('size') ? htmlspecialchars($this->request->query('size')) : $listSize;
        $start = $this->request->has('start') ? htmlspecialchars($this->request->query('start')) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->getFiles($path, $allowFiles);
        if (!count($files)) {
            return [
                "state" => "no match file",
                "list" => [],
                "start" => $start,
                "total" => count($files)
            ];
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = []; $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        return [
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ];

    }

    /**
     * 抓取远程图片
     * Date: 14-04-14
     * Time: 下午19:18
     */
    protected function actionCrawler(): array
    {
        set_time_limit(0);

        /* 上传配置 */
        $config = [
            "pathFormat" => $this->config['catcherPathFormat'],
            "maxSize" => $this->config['catcherMaxSize'],
            "allowFiles" => $this->config['catcherAllowFiles'],
            "oriName" => "remote.png"
        ];
        $fieldName = $this->config['catcherFieldName'];

        /* 抓取远程图片 */
        $list = [];
        $source = $this->request->input($fieldName);
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, [
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ]);
        }

        /* 返回抓取数据 */
        return [
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        ];
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getFiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getFiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> asset(substr($path2, strlen($_SERVER['DOCUMENT_ROOT']))),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /**
     * 保存附件信息
     * @param $fileInfo
     * @return mixed
     */
    private function storeFileInfo($fileInfo)
    {
        $attachment = [
            'album_id' => 0,
            'name' => $fileInfo['original'],
            'admin_id' => auth()->id() ?: 0,
            'path' => $fileInfo['url'],
            'mime_type' => $fileInfo['mime_type'],
            'size' => $fileInfo['size'],
        ];
        return Attachment::create($attachment);
    }
}
