<?php

namespace Modules\Imonitor\Repositories;

use Modules\Core\Repositories\BaseRepository;

/**
 * Interface ProductRepository
 * @package Modules\Imonitor\Repositories
 */
interface ProductRepository extends BaseRepository
{
    /**
     * @param $page
     * @param $take
     * @param $filter
     * @param $include
     * @return mixed
     */
    public function wherebyFilter($page, $take, $filter, $include);

    /**
     * @param $id
     * @return mixed
     */
    public function whereUser($id);

    /**
     * @param $id
     * @return mixed
     */
    public function whereVariable($id);
}
