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

    /**
     * 驼峰转小写
     * @param $str
     * @return string|string[]|null
     */
    public static function convertToLower($str, $glue = '_')
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', $glue . '$1', $str));
    }

    /**
     * 小写下划线转小驼峰
     * @param $str
     * @return string|string[]|null
     */
    public static function convertToUpper($str)
    {
        $str = preg_replace_callback('/_+([a-z])/',function($matches){
            return strtoupper($matches[1]);
        }, $str);
        return $str;
    }

    /**
     * 数组转字段串
     * @param $var
     * @return mixed
     */
    public static function arrToStr($var)
    {
        return str_replace(['array (', ')'], ['[', ']'], var_export($var, true));
    }

    /**
     * 将数组转换成字符串形式
     * @param $arr
     * @param string $prefix
     * @param int $timesSpace
     * @param bool $first
     * @return string
     */
    public static function convertArray($arr, $prefix = "\r\n", $timesSpace = 1, $first = true)
    {
        if (empty($arr)) {
            return '[]';
        }
        $space = "";
        for ($i = 1; $i <= $timesSpace; $i ++) {
            $space .= "    ";
        }
        $itemStr = '';
        $keys = array_keys($arr);
        $isIndexArr = false;
        if ($keys[0] === 0 && $keys[count($arr) - 1] === count($arr) - 1) {
            $isIndexArr = true;
        }
        foreach ($arr as $key => $value) {
            $itemStr .= $prefix . "        " . ($first ? '' : "    ");
            if (! $isIndexArr){
                $itemStr .= "    '" . $key . "' => ";

            }
            if (is_array($value)) {
                $itemStr .= self::convertArray($value, $prefix . "    ", $timesSpace + 1, false);
            } else {
                $itemStr .= "'" . $value . "',";
            }
        }
        return "[{$itemStr}
{$space}]" . ($first ? '' : ',');
    }

    /**
     * 解析多个id参数
     * @param $ids
     * @return array
     */
    public static function parseIds($ids): array
    {
        if (empty($ids)) {
            return [];
        }
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        if (!is_array($ids)) {
            return [];
        }
        $ids = array_filter(array_map(function($id) {
            return intval($id);
        }, $ids), function($id) {
            return !empty($id);
        });
        return $ids;
    }
}
