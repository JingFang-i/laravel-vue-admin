<?php


namespace App\Services\Auth;

use Jmhc\Admin\Service;
use Illuminate\Validation\Rule;

class AdminUserService extends Service
{

    protected function rules($data, $id): array
    {
        $required = is_null($id);
        $rule = [
            'username' => ['bail', Rule::requiredIf($required), 'string', 'max:50',
                'regex:/^[a-zA-Z0-9_\-@\.]{5,20}$/'],
            'name' => ['bail', Rule::requiredIf($required), 'string', 'max:15'],
            'password' => ['bail', Rule::requiredIf($required), 'string', 'confirmed'
//                , 'regex:/^.*(?=.{6,})(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^\.&*\?]).*$/'],
            , 'min:6', 'max:12'],
            'avatar' => ['bail', 'string', 'max:128'],
        ];
        if (is_null($id)) {
            $rule['username'][] = 'unique:admin_users';
        } else {
            $rule['username'][] = Rule::unique('admin_users')->ignore($id);
        }
        return $rule;
    }

    protected function message(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'username.max' => '用户名不能超过50个字符',
            'username.regex' => '用户名只能为数字字母或者_-@.符号',
            'name.required' => '姓名不能为空',
            'name.max' => '姓名最大长度为15个字符',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少为6位字符',
            'password.max' => '密码不能超过12位字符',
            'password.confirmed' => '确认密码不正确',
            'password.regex' => '密码至少6位，至少包含一个数字、一个字母和一个特殊字符（!@#$%^&*?.）',
        ];
    }

    /**
     * 查看
     *
     * @return mixed
     */
    public function index()
    {
        if (! $this->request->has('is_select')) {
            $lists = $this->repository->lists($this->request->query(), ['roles:id,name']);
            return $this->response->paginator($lists);
        } else {
            return $this->response->collection($this->repository->selectList());
        }
    }

    /**
     * 分配角色
     *
     * @return mixed
     */
    public function assignRole()
    {
        $adminId = $this->request->input('admin_id');
        $roleIds = $this->request->input('role_ids');
        if (!is_array($roleIds)){
            return $this->response->error('角色格式不正确');
        }
        if (empty($roleIds)) {
            return $this->response->error('角色不能为空');
        }
        $roleIds = array_map(function ($item) {
            return intval($item);
        }, $roleIds);
        $adminId = intval($adminId);
        if ($adminId === 1) {
            return $this->response->error('不能给超级管理员分配角色');
        }
        $admin = $this->repository->getById($adminId);
        if ($admin->syncRoles($roleIds)) {
            return $this->response->success();
        } else {
            return $this->response->error('分配角色错误');
        }
    }

    /**
     * 保存前置方法
     * @param array $data
     * @return array
     */
    protected function beforeStore(array $data): array
    {
        $data['avatar'] = isset($data['avatar']) && !empty($data['avatar']) ? $data['avatar'] : '';
        return $data;
    }


    /**
     * 删除前置方法
     * @param int $id
     * @return bool
     */
    protected function beforeDestroy(int $id): bool
    {
        if ($id == 1) {
            $this->errorMsg = '超级管理员不能删除';
            return false;
        }
    }


}
