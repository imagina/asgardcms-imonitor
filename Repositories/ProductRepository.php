<?php

namespace Modules\Imonitor\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ProductRepository extends BaseRepository
{
    public function wherebyFilter($page, $take, $filter, $include);
}
