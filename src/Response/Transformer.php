<?php


namespace Jmhc\Admin\Response;


use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Collection;

class Transformer
{

    protected $manager;

    public function __construct()
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new ArraySerializer());
    }

    /**
     * 分页资源数据转换
     *
     * @param $paginator
     * @param $transformer
     * @return array
     */
    public function paginator($paginator, $transformer = null)
    {
        $collection = $paginator->getCollection();
        if (!is_null($transformer)) {
            $resource = new Collection($collection, $transformer);
        } else {
            $resource = new Collection($collection, function($item) {
                return $item->toArray();
            });
        }
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * 资源数据
     *
     * @param $collection
     * @param $transformer
     * @return array
     */
    public function collection($collection, $transformer = null)
    {
        if (is_null($transformer)) {
            return $collection->toArray();
        }

        $resource = new Collection($collection, $transformer);
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * 单一资源数据
     *
     * @param $item
     * @param $transformer
     * @return array
     */
    public function item($item, $transformer = null)
    {
        if (is_null($transformer)) {
            return $item->toArray();
        }
        $resource = new Item($item, $transformer);
        return $this->manager->createData($resource)->toArray();
    }
}
