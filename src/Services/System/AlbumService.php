<?php


namespace Jmhc\Admin\Services\System;

use Jmhc\Admin\Service;

class AlbumService extends Service
{
    protected function rules(array $data, $id): array
    {
        return [
            'name' => 'bail|required|max:15',
            'cover_image' => 'max:255',
            'weigh' => 'integer',
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => '相册名称不能为空',
            'name.max' => '相册名称不能超过15个字符',
            'cover_image.max' => '封面图不能超过255个字符',
            'weigh.integer' => '权重值必须为整数',
        ];
    }

}
