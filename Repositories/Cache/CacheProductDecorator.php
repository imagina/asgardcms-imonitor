<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheProductDecorator extends BaseCacheDecorator implements ProductRepository
{
    public function __construct(ProductRepository $product)
    {
        parent::__construct();
        $this->entityName = 'imonitor.products';
        $this->repository = $product;
    }
    public function whereCategory($id)
    {
        return $this->remember(function () use ($id) {
            return $this->repository->whereVariable($id);
        });
    }

    public function wherebyFilter($page, $take, $filter, $include)
    {
        return $this->remember(function () use ($page, $take, $filter, $include) {
            return $this->repository->wherebyFilter($page, $take, $filter, $include);
        });
    }
}
