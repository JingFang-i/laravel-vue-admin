<?php


namespace Jmhc\Admin\Commands;

use Illuminate\Support\Facades\DB;
use Jmhc\Admin\Utils\Helper;

class CreateAuth
{
    const PERMISSION_TABLE_NAME = 'permissions';

    protected $prefix = '';
    protected $name = '';
    protected $module = 'admin';

    protected $operates = [
        'index' => '列表',
        'show'  => '查看',
        'update' => '更新',
        'store'  => '添加',
        'delete' => '删除',
        'multi'  => '批量更新',
        'multi-del' => '批量删除',
    ];

    public function __construct($prefix, $name, $module)
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->module = strtolower($module);
    }

    public function run()
    {
        $lowerName = Helper::convertToLower($this->name, '-');
        $pid = 0;
        if ($this->prefix) {
            $pid = $this->insertParent();
            $componentPath = '/' . Helper::convertToLower($this->prefix, '-') . '/' . $lowerName;
        } else {
            $componentPath = '/' . $lowerName . '/index';
        }
        // 插入数据
        $title = ucfirst(Helper::convertToLower($this->name, ''));
        $authId = $this->_createData($title, $this->name, $lowerName, $componentPath, $lowerName, $pid);
        $this->_createPermissions($authId, $lowerName);
    }

    /**
     * 创建父级菜单
     * @return int
     */
    protected function insertParent()
    {
        $title = ucfirst(Helper::convertToLower($this->prefix, ''));
        $rule = Helper::convertToLower($this->prefix, '-');
        $routePath = '/' . $rule;
        return $this->_createData($title, '', $rule, '', $routePath);
    }

    /**
     * 创建操作权限
     * @param $parentId
     * @param $lowerName
     */
    private function _createPermissions($parentId, $lowerName)
    {
        foreach ($this->operates as $operate => $title) {
            $this->_createData($title, '', $lowerName . '.' . $operate, '', '', $parentId, 0);
        }
    }

    /**
     * 创建数据并插入数据库
     * @param $title
     * @param $name
     * @param $rule
     * @param $componentPath
     * @param $routePath
     * @param int $isMenu
     * @return int
     */
    private function _createData($title, $name, $rule, $componentPath, $routePath, $pid = 0, $isMenu = 1)
    {
        $data = [
            'title' => $title,
            'icon' => 'list',
            'pid' => $pid,
            'name' => $rule,
            'component_path' => $componentPath,
            'view_route_name' => $name,
            'view_route_path' => $routePath,
            'redirect_path' => '',
            'guard_name' => $this->module,
            'is_menu' => $isMenu,
            'is_hidden' => 0,
            'weigh' => 100,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $permission = DB::table(self::PERMISSION_TABLE_NAME)->where('name', $data['name'])->first();
        if (!empty($permission)) {
            DB::table(self::PERMISSION_TABLE_NAME)->where('id', $permission->id)->update($data);
            return $permission->id;
        } else {
            return DB::table(self::PERMISSION_TABLE_NAME)->insertGetId($data);
        }

    }
}