<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Imonitor\Repositories\AlertRepository;

class CacheAlertDecorator extends BaseCacheDecorator implements AlertRepository
{
    public function __construct(AlertRepository $alert)
    {
        parent::__construct();
        $this->entityName = 'imonitor.alerts';
        $this->repository = $alert;
    }


    public function WhereByProduct($id)
    {

        return $this->remember(function () use ($id) {
            return $this->repository->WhereByProduct($id);
        });
    }

    public function getItemsBy($params = false)
    {
        return $this->remember(function () use ($params) {
            return $this->repository->getItemsBy($params);
        });
    }
}
