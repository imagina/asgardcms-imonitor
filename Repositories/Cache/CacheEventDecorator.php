<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Imonitor\Repositories\EventRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheEventDecorator extends BaseCacheDecorator implements EventRepository
{
    public function __construct(EventRepository $event)
    {
        parent::__construct();
        $this->entityName = 'imonitor.events';
        $this->repository = $event;
    }

    /**
     * @param $criteria
     * @param bool $params
     * @return mixed
     */
    public function getItem($criteria, $params = false)
    {
        return $this->remember(function () use ($criteria, $params) {
            return $this->repository->getItem($criteria, $params);
        });
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getItemsBy($params = false)
    {
        return $this->remember(function () use ($params) {
            return $this->repository->getItemsBy($params);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereProduct($id)
    {
      return $this->remember(function () use ($id) {
        return $this->repository->whereProduct($id);
    });
    }
}
