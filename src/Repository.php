<?php


namespace Jmhc\Admin;


use Illuminate\Support\Str;
use Jmhc\Admin\Contracts\Repository as RepositoryInterface;
use Jmhc\Admin\Factories\ServiceBindFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 */
abstract class Repository implements RepositoryInterface
{

    const PER_PAGE = 10; //每页的记录数

    /**
     * @var Model
     */
    protected $model;
    protected $allowFields = ['*'];
    protected $orderField  = 'created_at';
    protected $orderType   = 'desc';

    protected $defaultSelectFields = ['id', 'name'];


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * 获取分页数据
     *
     * @return mixed
     */
    public function lists(array $params, array $with = [], array $extraWhere = [])
    {
        list($normalFilters, $relationFilters, $normalOperate, $relationOperate) = $this->parseParams($params);

        $where = $this->buildWhereByParams($normalFilters, $normalOperate);
        if ($where) {
            $this->model = $this->model->where($where);
        }
        if ($relationFilters) {
            foreach ($relationFilters as $relationName => $filter) {
                $relationWhere = $this->buildWhereByParams($filter, $relationOperate[$relationName]);
                if ($relationWhere) {
                    $this->model = $this->model->whereHas($relationName, function($query) use($relationWhere) {
                        $query->where($relationWhere);
                    });
                }
            }
        }

        $pageSize = isset($params['page_size']) ? $params['page_size'] : self::PER_PAGE;
        $pageSize = $pageSize > 500 ? 500 : $pageSize;

        return $this->model->select($this->allowFields)
            ->when($with, function ($query, $with) {
                return $query->with($with);
            })
            ->when($extraWhere, function ($query, $extraWhere) {
                return $query->where($extraWhere);
            })
            ->when($this->orderField, function ($query, $orderField) {
                return $query->orderBy($orderField, $this->orderType);
            })
            ->paginate($pageSize);
    }

    /**
     * 选择列表
     *
     * @param array $fields
     * @return mixed
     */
    public function selectList(array $fields = [], array $where = [])
    {
        if (empty($fields)) {
            $fields = $this->defaultSelectFields;
        }
        return $this->model->select($fields)
            ->when($where, function($query, $where){
                return $query->where($where);
            })
            ->when($this->orderField, function ($query, $orderField) {
                return $query->orderBy($orderField, $this->orderType);
            })
            ->get();
    }

    /**
     * 保存
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }


    /**
     * 更新
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $row = $this->model->find($id);
        if (!$row) {
            return false;
        }
        return $row->fill($data)->save();
    }

    /**
     * 获取一条记录
     *
     * @param $id
     * @return mixed
     */
    public function show(int $id, array $with = [], array $fields = [])
    {
        if (empty($fields)) {
            $fields = $this->allowFields;
        }
        return $this->model->when($with, function ($query, $when) {
            return $query->with($when);
        })
            ->when($fields, function ($query, $fields) {
                return $query->select($fields);
            })
            ->find($id);
    }

    /**
     * 删除
     *
     * @param $id
     * @return int|mixed
     */
    public function destroy(int $id)
    {
        return $this->model->destroy($id);
    }

    /**
     * 批量删除
     * @param array $ids
     * @return int
     */
    public function multiDestroy(array $ids)
    {
        return $this->model->destroy($ids);
    }

    /**
     * 通过id查询
     *
     * @param $roleId
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->model->find($id);
    }


    /**
     * 解析参数
     * @param array $params
     * @return mixed
     */
    protected function parseParams(array $params)
    {
        $filter = $this->decodeParam('filter', $params);
        $operate = $this->decodeParam('operate', $params);
        if (empty($filter) || empty($operate)) {
            return [[], [], [], []];
        }
        $normalFilters = $relationFilters = $normalOperate = $relationOperate = [];
        foreach ($filter as $field => $value) {
            if (strpos($field, '.')){
                $fieldArr = explode('.', $field);
                $name = array_pop($fieldArr);
                $relationName = join('.', $fieldArr);
                $relationName = Str::camel($relationName);
                if (!isset($relationName, $relationFilters)) {
                    $relationFilters[$relationName] = [$name => $value];
                    $relationOperate[$relationName] = [$name => isset($operate[$field]) ? $operate[$field] : '='];
                } else {
                    $relationFilters[$relationName][$name] = $value;
                    $relationOperate[$relationName][$name] = isset($operate[$field]) ? $operate[$field] : '=';
                }
            } else {
                $normalFilters[$field] = $value;
                $normalOperate[$field] = isset($operate[$field]) ? $operate[$field] : '=';
            }
        }
        return [$normalFilters, $relationFilters, $normalOperate, $relationOperate];
    }

    /**
     * 通过参数构建where条件
     *
     * @param $params
     * @return bool|\Closure
     */
    protected function buildWhereByParams($filter, $operate)
    {
        $where = function($query) use ($filter, $operate) {
            foreach ($filter as $k => $v) {
                $op = isset($operate[$k]) ? $operate[$k] : '=';
                $v = !is_array($v) ? trim($v) : $v;
                if ($v === '') {
                    continue;
                }
                switch($op) {
                    case '=':
                    case '<>':
                        $query->where($k, $op, (string)$v);
                        break;
                    case '>':
                    case '<':
                    case '<=':
                    case '>=':
                        $query->where($k, $op, intval($v));
                        break;
                    case 'in':
                        $val = $this->convert2Array($v);
                        if (empty($val)) {
                            break;
                        }
                        $query->whereIn($k, $val);
                        break;
                    case 'not in':
                        $val = $this->convert2Array($v);
                        if (empty($val)) {
                            break;
                        }
                        $query->whereNotIn($k, $val);
                        break;
                    case 'between':
                        $val = $this->convert2Array($v);
                        if (empty($val)) {
                            break;
                        }
                        $arr = array_slice($val, 0, 2);
                        if (count($arr) !== 2) {
                            break;
                        }
                        //当出现一边为空时改变操作符
                        if ($arr[0] === '') {
                            $query->where($k, '<=', $arr[1]);
                        } elseif ($arr[1] === '') {
                            $query->where($k, '>=', $arr[0]);
                        } else {
                            $query->whereBetween($k, $val);
                        }
                        break;
                    case 'not between':
                        $val = $this->convert2Array($v);
                        if (empty($val)) {
                            break;
                        }
                        $arr = array_slice($val, 0, 2);
                        if (count($arr) !== 2) {
                            break;
                        }
                        //当出现一边为空时改变操作符
                        if ($arr[0] === '') {
                            $query->where($k, '>', $arr[1]);
                        } elseif ($arr[1] === '') {
                            $query->where($k, '<', $arr[0]);
                        } else {
                            $query->whereNotBetween($k, $val);
                        }
                        break;
                    case 'range':
                    case 'not range':
                        $val = $this->convert2Array($v);
                        if (empty($val)) {
                            break;
                        }
                        $rangeArr = array_map(function($item){
                            return strtotime(trim($item));
                        }, $val);
                        if (count($rangeArr) !== 2) {
                            break;
                        }
                        if ($rangeArr[0] === $rangeArr[1]) {
                            $rangeArr[1] = $rangeArr[0] + 86400;
                        }
                        $rangeArr = array_map(function($item){
                            return date('Y-m-d H:i:s', $item);
                        }, $rangeArr);
                        $op === 'range' ?
                            $query->whereBetween($k, $rangeArr) :
                            $query->whereNotBetween($k, $rangeArr);
                        break;
                    case 'like':
                    case 'not like':
                        $v = (string)$v;
                        $query->where($k, $op, "%{$v}%");
                        break;
                    default:
                        break;
                }
            }
            return $query;
        };
        return $where;

    }

    /**
     * 将值转换成数组
     * @param $value
     * @return array|false|string[]
     */
    private function convert2Array($value)
    {
        return is_array($value) ? $value : (strpos($value, ',') !== false ? explode(',', $value) : [$value]);
    }


    /**
     * 将参数中的json转换成数组
     * @param $name
     * @param $params
     * @return array|mixed
     */
    protected function decodeParam($name, $params)
    {
        return isset($params[$name])
            ? json_decode($params[$name], true)
            : [];
    }

    /**
     * 实例化服务类
     * @return \Jmhc\Admin\Contracts\Repository|static::class
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function instance()
    {
        $modelName = explode("\\Repositories\\", static::class)[1];
        $modelName = substr($modelName, 0, -10); //去掉Repository后缀
        return (new ServiceBindFactory($modelName))->getRepository(false);
    }

    /**
     * 返回Model
     * @return
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * 可直接调用model方法
     * @param $name
     * @param $arguments
     * @mixin \Illuminate\Database\Eloquent\Model
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->model->$name(...$arguments);
    }


}
