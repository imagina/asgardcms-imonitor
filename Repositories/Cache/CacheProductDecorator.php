<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

/**
 * Class CacheProductDecorator
 * @package Modules\Imonitor\Repositories\Cache
 */
class CacheProductDecorator extends BaseCacheDecorator implements ProductRepository
{
    /**
     * CacheProductDecorator constructor.
     * @param ProductRepository $product
     */
    public function __construct(ProductRepository $product)
    {
        parent::__construct();
        $this->entityName = 'imonitor.products';
        $this->repository = $product;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereVariable($id)
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereVariable($id);
        });
    }

    /**
     * @param $page
     * @param $take
     * @param $filter
     * @param $include
     * @return mixed
     */
    public function wherebyFilter($page, $take, $filter, $include)
    {
        return $this->remember(function () use ($page, $take, $filter, $include) {
            return $this->repository->wherebyFilter($page, $take, $filter, $include);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereUser($id)
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereUser($id);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereOperator($id)
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereOperator($id);
        });
    }
}
