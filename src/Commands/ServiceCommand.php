<?php

namespace Jmhc\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jmhc\Admin\Utils\Helper;

class ServiceCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:generate {table} {--model=} {--module=Admin} {--force} {--view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一键生成控制器、模型、服务、仓储类';

    protected $fileSystem;

    protected $multiFieldsPrefix = ['status', 'is_'];

    protected $routePath = '';

    protected $modelName = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->fileSystem = new Filesystem();
        $this->routePath = base_path('routes/admin.php');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->argument('table'); //不带前缀数据表名
        $modelName = $this->option('model');
        $moduleName = $this->option('module');
        $force = $this->option('force'); //是否强制
        $view = $this->option('view'); // 是否生成视图
        if (!$modelName) {
            $this->error('缺少模型名称');
            return false;
        }

        $modelName = str_replace("\\", '/', $modelName);
        $modelNameArr = explode('/', $modelName);
        $prefix = '';
        if (count($modelNameArr) > 1) {
            $prefix = join('/', array_slice($modelNameArr, 0, -1));
        }
        $modelName = $modelNameArr[count($modelNameArr) - 1];
        $this->modelName = $modelName;

        $controllerPath = $this->appPath(config('admin.controller_prefix'), $moduleName, $prefix);
        $modelPath = $this->appPath(config('admin.model_prefix'), $moduleName, $prefix);
        $servicePath = $this->appPath(config('admin.service_prefix'), $moduleName, $prefix);
        $repositoryPath = $this->appPath(config('admin.repository_prefix'), $moduleName, $prefix);

        if (!$this->fileSystem->exists($controllerPath)) {
            $this->fileSystem->makeDirectory($controllerPath, 0755, true);
        }
        if (!$this->fileSystem->exists($modelPath)) {
            $this->fileSystem->makeDirectory($modelPath, 0755, true);
        }
        if (!$this->fileSystem->exists($servicePath)) {
            $this->fileSystem->makeDirectory($servicePath, 0755, true);
        }
        if (!$this->fileSystem->exists($repositoryPath)) {
            $this->fileSystem->makeDirectory($repositoryPath, 0755, true);
        }

        $tableName = config('database.connections.mysql.prefix') . $table;
        $dbName = config('database.connections.mysql.database');
        $tableInfo = DB::select('SELECT * FROM `information_schema`.`columns` WHERE TABLE_SCHEMA = ? AND table_name = ? ORDER BY ORDINAL_POSITION', [$dbName, $tableName]);
        $parsedTable = $this->parseTableInfo($tableInfo);
        $selectOptions = $this->makeSelectStr($parsedTable['fieldInfo']);

        $controllerNamespace = config('admin.controller_prefix') . ($prefix ? "\\" . $prefix : '');
        $controllerNamespace = str_replace('{:moduleName}', $moduleName, $controllerNamespace);
        //控制器模板
        $controllerStub = str_replace([
            '{%namespace%}', '{%name%}'
        ], [
            $controllerNamespace, $modelName
        ], $this->getStub('controller'));

        $controllerFilePath = $controllerPath . '/' . $modelName . 'Controller.php';
        if ($this->fileSystem->exists($controllerFilePath) && !$force) {
            $this->error('controller已存在，如需覆盖请加上--force参数');
            return false;
        }
        $this->fileSystem->put($controllerFilePath, $controllerStub);
        $this->info('controller创建成功!');

        //服务模板
        $serviceNamespace = config('admin.service_prefix') . ($prefix ? "\\" . $prefix : '');
        $multiFields = Helper::convertArray($parsedTable['multiFields']);
        $rule = Helper::convertArray($parsedTable['rule'], "\r\n", 2);
        $message = Helper::convertArray($parsedTable['message'], "\r\n", 2);
        $serviceStub = str_replace([
            '{%namespace%}', '{%name%}', '{%multiFields%}', '{%rule%}', '{%message%}', '{%attr%}', '{%function%}'
        ], [
            $serviceNamespace, $modelName, $multiFields, $rule, $message, $selectOptions['serviceAttr'], $selectOptions['serviceAttrFunc']
        ], $this->getStub('service'));
        $serviceFilePath = $servicePath . '/' . $modelName . 'Service.php';
        if ($this->fileSystem->exists($serviceFilePath) && !$force) {
            $this->error('service已存在，如需覆盖请加上--force参数');
            return false;
        }
        $this->fileSystem->put($serviceFilePath, $serviceStub);
        $this->info('service创建成功!');

        //仓储模板
        $repositoryNamespace = config('admin.repository_prefix') . ($prefix ? "\\" . $prefix : '');
        $allowFields = array_keys($parsedTable['fieldInfo']);
        $allowFieldStr =  Helper::convertArray($allowFields);

        $orderField = $parsedTable['sortField'] ? "'{$parsedTable['sortField']}'" : "'created_at'";
        $repositoryStub = str_replace([
            '{%namespace%}', '{%name%}', '{%allowFields%}', '{%orderField%}'
        ], [
            $repositoryNamespace, $modelName, $allowFieldStr, $orderField
        ], $this->getStub('repository'));
        $repositoryFilePath = $repositoryPath . '/' . $modelName . 'Repository.php';
        if ($this->fileSystem->exists($repositoryFilePath) && !$force) {
            $this->error('repository已存在，如需覆盖请加上--force参数');
            return false;
        }
        $this->fileSystem->put($repositoryFilePath, $repositoryStub);
        $this->info('repository创建成功!');

        //模型模板
        $modelNamespace = config('admin.model_prefix') . ($prefix ? "\\" . $prefix : '');
        $fillable = Helper::convertArray(array_values(array_filter($allowFields, function($field){
            return !in_array($field, ['id', 'created_at', 'updated_at']);
        })));
        $serviceClass = $serviceNamespace . "\\" . $modelName . 'Service';
        $modelStub = str_replace([
            '{%namespace%}', '{%service%}', '{%name%}', '{%table%}', '{%fillable%}', '{%append%}', '{%function%}'
        ], [
            $modelNamespace, $serviceClass, $modelName, "'$table'", $fillable, $selectOptions['modelAppendAttr'], $selectOptions['modelAppendAttrFunc']
        ], $this->getStub('model'));
        $modelFilePath = $modelPath . '/' . $modelName . '.php';
        if ($this->fileSystem->exists($modelFilePath) && !$force){
            $this->error('model已存在，如需覆盖请加上--force参数');
            return false;
        }
        $this->fileSystem->put($modelFilePath, $modelStub);
        $this->info('model创建成功!');
        //生成路由
        $this->makeRoute($prefix, $modelName);
        $this->info('路由创建成功!');

        //创建菜单权限
        (new CreateAuth($prefix, Helper::convertToUpper($table), $moduleName))->run();
        $this->info('菜单和权限创建成功');
        // 清除缓存
        $this->call('cache:clear');

        if (strtolower($moduleName) === 'admin' && $view) {
            //创建视图
            (new CreatePage(Helper::convertToUpper($table), $prefix, $parsedTable['fieldInfo']))->run();
            $this->info('页面创建成功');
        }
    }


    /**
     * 返回app路径
     * @param $classPrefix
     * @return string
     */
    protected function appPath($classPrefix, $module, $prefix = '')
    {
        $path = app_path(str_replace(["App\\", "\\", '{:moduleName}'], ['', '/', $module], $classPrefix));
        if ($prefix) {
            $path .= '/' . $prefix;
        }
        return $path;
    }

    /**
     * 获取模板
     * @param $type
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub($type)
    {

        switch($type) {
            case 'controller':
                $path = __DIR__ . '/stubs/controller.stub';
                break;
            case 'model':
                $path = __DIR__ . '/stubs/model.stub';
                break;
            case 'service':
                $path = __DIR__ . '/stubs/service.stub';
                break;
            case 'repository':
                $path = __DIR__ . '/stubs/repository.stub';
                break;
            default:
                $path = '';
        }
        return $this->fileSystem->get($path);

    }

    /**
     * 添加路由
     * @param $namespacePrefix
     * @param $modelName
     */
    protected function makeRoute($prefix, $modelName)
    {
        $routeName = Str::plural(Helper::convertToLower($modelName, '-'));
        $controllerPath = $prefix ? str_replace('/', "\\", $prefix) . "\\" : '';
        $data = "\r\n// " . $modelName;
        $data .= "\r\n" . sprintf("Route::resource('%s', '%s%s');", $routeName,
            $controllerPath, $modelName . 'Controller');
        $this->fileSystem->append($this->routePath, $data);
    }

    /**
     * 如果有选择列表字段，则自动生成获取器
     * @param $fieldInfo
     */
    private function makeSelectStr($fieldInfo)
    {
        $options = [
            'modelAppendAttr' => '',
            'modelAppendAttrFunc' => [],
            'serviceAttr' => [],
            'serviceAttrFunc' => []
        ];

        $appendAttr = [];
        foreach ($fieldInfo as $field => $item){
            if (!empty($item['selectList'])) {
                $appendAttr[] = $field . '_text';

                $field = Helper::convertToUpper($field);
                $options['modelAppendAttrFunc'][] = $this->modelAppendAttrFunction($field);
                $options['serviceAttr'][] = $this->serviceAppendSelectAttr($field, $item['selectList']);
                $options['serviceAttrFunc'][] = $this->serviceAppendFunction($field);
            }
        }
        $options['modelAppendAttr'] = Helper::convertArray($appendAttr);
        $options['modelAppendAttrFunc'] = join("\r\n\r\n\r\n", $options['modelAppendAttrFunc']);
        $options['serviceAttr'] = join("\r\n\r\n\r\n", $options['serviceAttr']);
        $options['serviceAttrFunc'] = join("\r\n\r\n\r\n", $options['serviceAttrFunc']);
        return $options;
    }

    /**
     * 解析表格字段
     * @param $tableInfo
     * @return array
     */
    private function parseTableInfo($tableInfo)
    {
        $tableParsedResult = [
            'multiFields' => [],
            'rule' => [],
            'message' => [],
            'sortField' => "",
            'fieldInfo' => []
        ];
        foreach ($tableInfo as $item) {
            foreach ($this->multiFieldsPrefix as $v) {
                if (strpos($item->COLUMN_NAME, $v) !== false) {
                    $tableParsedResult['multiFields'][] = $item->COLUMN_NAME;
                }
            }
            $parsedResult = $this->parseColumn($item);
            if ($item->COLUMN_NAME !== 'id' && !empty($parsedResult['rule'])) {
                $tableParsedResult['rule'][$item->COLUMN_NAME] = $parsedResult['rule'];
                $tableParsedResult['message'] = array_merge($tableParsedResult['message'], $parsedResult['message']);
                if ($parsedResult['is_order_field']) {
                    $tableParsedResult['sortField'] = $item->COLUMN_NAME;
                }
            }

            $tableParsedResult['fieldInfo'][$item->COLUMN_NAME] = [
                'field' => $parsedResult['field'],
                'title' => $parsedResult['title'],
                'type' => $parsedResult['type'],
                'selectList' => $parsedResult['selectList'],
                'rule' => $parsedResult['rule'],
            ];
        }
        return $tableParsedResult;
    }

    /**
     * 解析
     * @param $columnInfo
     * @return array
     */
    private function parseColumn($columnInfo)
    {
        $column = [
            'field' => $columnInfo->COLUMN_NAME,
            'rule' => [],
            'message' => [],
            'title' => $columnInfo->COLUMN_COMMENT,
            'selectList' => [],
            'type' => 'text',
            'is_order_field' => $columnInfo->COLUMN_NAME === 'weigh',
        ];

        switch ($columnInfo->DATA_TYPE) {
            case 'tinyint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                $column['rule'][] = 'numeric';
                $column['message'][$columnInfo->COLUMN_NAME . '.numeric'] = $columnInfo->COLUMN_COMMENT . '必须为一个数字';
                $column['type'] = 'number';
                if ($columnInfo->DATA_TYPE === 'tinyint'){
                    if (strpos($columnInfo->COLUMN_COMMENT, ':')) {
                        $column['type'] = 'select';
                        $arr = explode(':', $columnInfo->COLUMN_COMMENT);
                        $column['title'] = $arr[0];
                        if (isset($arr[1]) && $arr[1]){
                            $listStr = explode(',', $arr[1]);
                            $selectList = [];
                            foreach ($listStr as $valueStr){
                                $listItem = explode('=', $valueStr);
                                if (count($listItem) === 2) {
                                    $selectList[$listItem[0]] = $listItem[1];
                                }
                            }
                            if (count($selectList) === 2) {
                                $selectKeys = array_keys($selectList);
                                if ($selectKeys[0] == 0 && $selectKeys[1] == 1) {
                                    $column['type'] = 'switch';
                                }
                            }
                            $column['selectList'] = $selectList;
                        }

                    }
                }
                break;
            case 'decimal':
                if (strpos($columnInfo->COLUMN_COMMENT, '价格')) {
                    $column['type'] = 'price';
                }
                break;
            case 'char':
            case 'varchar':
                $column['rule'][] = 'max:' . $columnInfo->CHARACTER_MAXIMUM_LENGTH;
                $column['message'][$columnInfo->COLUMN_NAME . '.max'] = $columnInfo->COLUMN_COMMENT . '不能超过' . $columnInfo->CHARACTER_MAXIMUM_LENGTH . '个字符';
                break;
            case 'text':
                $column['type'] = 'editor';
                break;
            case 'json':
                $column['rule'][] = 'array';
                $column['message'][$columnInfo->COLUMN_NAME . '.array'] = $columnInfo->COLUMN_COMMENT . '格式不正确';
                break;
            case 'timestamp':
            case 'date':
                $column['type'] = 'date';
                break;
            default:
                $column['type'] = 'text';
        }
        if (is_null($columnInfo->COLUMN_DEFAULT) && !in_array($columnInfo->DATA_TYPE, ['text', 'json', 'timestamp'])) {
            array_unshift($column['rule'], 'required');
            $column['message'][$columnInfo->COLUMN_NAME . '.required'] = $column['title'] . '不能为空';
        }
        $columnArr = explode('_', $column['field']);
        $suffix = $columnArr[count($columnArr) - 1];
        if (in_array($suffix, ['img', 'image', 'logo', 'avatar'])) {
            $column['type'] = 'image';
        }
        if (in_array($suffix, ['imgs', 'images'])) {
            $column['type'] = 'images';
        }

        return $column;
    }

    /**
     * 服务选择列表属性
     * @param $attrName
     * @param $selectList
     * @return string
     */
    private function serviceAppendSelectAttr($attrName, $selectList)
    {
        $content = Helper::convertArray($selectList);
        return <<<EOF
    protected \${$attrName}Map = {$content};
EOF;

    }

    /**
     * 获取选择列表文字
     * @param $attrName
     * @return string
     */
    private function serviceAppendFunction($attrName)
    {
        $attrNameFunc = ucfirst($attrName);
        return <<<EOF
    public function get{$attrNameFunc}(\$value)
    {
        return isset(\$this->{$attrName}Map[\$value]) ? \$this->{$attrName}Map[\$value] : '无';
    }
EOF;

    }

    /**
     * 在模型中添加属性获取器方法
     * @param $attrName
     * @return string
     */
    private function modelAppendAttrFunction($attrName)
    {
        $attrName = ucfirst($attrName);
        $fieldName = Helper::convertToLower($attrName);
        return <<<EOF
    public function get{$attrName}TextAttribute(\$value)
    {
        return {$this->modelName}Service::instance()->get{$attrName}(\$this->{$fieldName});
    }
EOF;
    }
}
