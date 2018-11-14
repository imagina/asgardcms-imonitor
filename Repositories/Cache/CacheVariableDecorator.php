<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Imonitor\Repositories\VariableRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVariableDecorator extends BaseCacheDecorator implements VariableRepository
{
    public function __construct(VariableRepository $variable)
    {
        parent::__construct();
        $this->entityName = 'imonitor.variables';
        $this->repository = $variable;
    }
}
