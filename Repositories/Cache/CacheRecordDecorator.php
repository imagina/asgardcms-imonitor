<?php

namespace Modules\Imonitor\Repositories\Cache;

use Modules\Imonitor\Repositories\RecordRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheRecordDecorator extends BaseCacheDecorator implements RecordRepository
{
    public function __construct(RecordRepository $record)
    {
        parent::__construct();
        $this->entityName = 'imonitor.records';
        $this->repository = $record;
    }
}
