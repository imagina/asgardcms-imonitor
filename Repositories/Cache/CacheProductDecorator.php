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
}
