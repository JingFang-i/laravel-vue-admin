<?php


namespace Jmhc\Admin\Commands;

use Illuminate\Filesystem\Filesystem;
use Jmhc\Admin\Utils\Helper;

class CreatePage
{

    protected $name = '';
    protected $prefix = '';
    protected $fieldInfo = [];
    protected $fieldMap = [];

    protected $filesystem = null ;

    public function __construct(string $name, string $prefix, array $fieldInfo)
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->fieldInfo = $fieldInfo;
        $this->filesystem = new Filesystem();
        $this->fieldMap = require __DIR__ . '/fieldmap.php';
    }

    public function run()
    {
        $fieldRule = $rules = [];
        $operations = ['add', 'edit', 'del'];
        foreach ($this->fieldInfo as $field => $info) {
            if (!in_array($field, ['id', 'created_at', 'updated_at', 'weigh'])) {
                $rules[$field] = $this->_createRule($info['title'], $info['rule']);
            }
            $fieldRule[] = $this->_createFields($info);
            if ($field === 'weigh') {
                array_unshift($operations, 'sort');
            }
        }

        $this->_createApiPage();
        $this->_createViewPage($fieldRule, $rules, $operations);
    }

    private function _createRule($title, $rules)
    {
        $ruleArr = [];
        foreach ($rules as $rule) {
            if ($rule === 'numeric') {
                $ruleArr[] = [
                    'type' => 'number',
                    'message'=> $title . '必须为一个数字',
                    'trigger' => 'blur'
                ];
            }
            if ($rule === 'required') {
                $ruleArr[] = [
                    'required' => true,
                    'message' => $title . '不能为空',
                    'trigger' => 'blur'
                ];
            }
            if ($rule === 'array') {
                $ruleArr[] = [
                    'type' =>'array',
                    'message' => $title . '格式不正确',
                    'trigger' => 'change',
                ];
            }
            if (strpos($rule, 'max') !== false) {
                [$name, $limit] = explode(':', $rule);
                $ruleArr[] = [
                    'max' => $limit,
                    'message' => $title . '长度不能超过' . $limit,
                    'trigger' => 'blur'
                ];
            }
        }
        return $ruleArr;
    }

    private function _createFields($info)
    {
        $fields = [
            'field' => $info['field'],
            'label' => $info['title'] ? $info['title'] : (isset($this->fieldMap[$info['field']]) ?
                $this->fieldMap[$info['field']] : ucfirst($info['field'])),
            'type' => $info['type'],
        ];
        if (!empty($info['selectList'])) {
            $fields['selectList'] = $info['selectList'];
        }
        if (in_array($info['field'], ['id', 'weigh', 'created_at', 'updated_at'])) {
            $fields['editable'] = false;
        }
        if ($info['type'] === 'image' || $info['type'] === 'images') {
            $fields['searchable'] = false;
        }
        if (in_array($info['type'], ['date', 'datetime'])) {
            $fields['operate'] = 'range';
        }
        return $fields;
    }

    private function _createApiPage()
    {
        if ($this->prefix) {
            $prefix = Helper::convertToLower($this->prefix, '-');
            $apiDir = resource_path('page/api/' . $prefix);
        } else {
            $apiDir = resource_path('page/api');
        }
        if (!$this->filesystem->exists($apiDir)) {
            $this->filesystem->makeDirectory($apiDir);
        }
        $name = Helper::convertToLower($this->name, '-');
        $apiPath = $apiDir . '/' . $name . '.js';

        $template = $this->filesystem->get(__DIR__ . '/stubs/api.stub');
        $template = str_replace('{%name%}', $name, $template);
        $this->filesystem->put($apiPath, $template);
    }

    private function _createViewPage($fieldRule, $rules, $operations)
    {
        $name = Helper::convertToLower($this->name, '-');
        $path = $name;
        if ($this->prefix) {
            $prefix = Helper::convertToLower($this->prefix, '-');
            $viewDir = resource_path('page/views/' . $prefix);
            $viewPath = $viewDir . '/' . $name . '.vue';
            $path = $prefix . '/' . $name;
        } else {
            $viewDir = resource_path('page/views/' . $name);
            $viewPath = $viewDir . '/index.vue';
        }
        if (!$this->filesystem->exists($viewDir)) {
            $this->filesystem->makeDirectory($viewDir);
        }
        $fieldRule = json_encode($fieldRule, JSON_UNESCAPED_UNICODE);
        $rules = json_encode($rules, JSON_UNESCAPED_UNICODE);
        $operations = json_encode($operations, JSON_UNESCAPED_UNICODE);

        $template = $this->filesystem->get(__DIR__ . '/stubs/page.stub');
        $template = str_replace(
            ['{%path%}', '{%name%}', '{%operations%}',  '{%rule%}', '{%fields%}'],
            [$path, $name, $operations, $rules, $fieldRule], $template);
        $this->filesystem->put($viewPath, $template);
    }

}
