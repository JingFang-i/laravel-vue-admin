<?php


namespace Jmhc\Admin\Traits;


use Illuminate\Support\Facades\Validator;

trait HasValidate
{

    /**
     * 验证
     *
     * @param $data
     * @return bool
     */
    public function validate(array $data, int $id = null)
    {
        $rule = $this->rules($data, $id);
        $message = $this->message();
        if (!empty($rule)) {
            $validator = Validator::make($data, $rule, $message);
            if ($validator->fails()) { //失败
                $this->errorMsg = $validator->errors()->first();
            }
            return !$validator->fails();
        }
        return true;
    }

    /**
     * 定义验证规则
     *
     * @param $data
     * @param $id
     * @return array
     */
    protected function rules(array $data, $id): array
    {
        return [];
    }

    /**
     * 自定义错误信息
     *
     * @return array
     */
    protected function message(): array
    {
        return [];
    }

}
