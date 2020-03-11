<?php


namespace Jmhc\Admin\Utils;


class Helper
{
    /**
     * 数组转换成树结构
     * @param array $data 数据源
     * @param int $parentId 父级ID
     * @param string $parentName 父级字段名称
     * @return array
     */
    public static function array2Tree(array $data, string $parentName = 'pid', int $parentId = 0, bool $isReturnEmpty = true): array
    {
        $arr = [];
        foreach ($data as $item) {
            if ($item[$parentName] == $parentId) {
                $children = self::array2Tree($data, $parentName, $item['id'], $isReturnEmpty);
                // 当不返回空值并且children为空的时候不返回
                if (! $isReturnEmpty && empty($children)) {
                    $arr[] = $item;
                    continue;
                }
                $item['children'] = $children;
                $arr[] = $item;
            }
        }
        return $arr;
    }
}
