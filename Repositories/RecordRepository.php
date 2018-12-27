<?php

namespace Modules\Imonitor\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface RecordRepository extends BaseRepository
{

    /**
     * @param $page
     * @param $take
     * @param $filter
     * @param $include
     * @return mixed
     */
    public function wherebyFilter($page, $take, $filter, $include);
}
