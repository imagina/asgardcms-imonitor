<?php

namespace Modules\Imonitor\Repositories;

use Modules\Core\Repositories\BaseRepository;

/**
 * Interface AlertRepository
 * @package Modules\Imonitor\Repositories
 */
interface AlertRepository extends BaseRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function WhereByProduct($id);

    /**
     * @param bool $params
     * @return mixed
     */
    public function getItemsBy($params = false);
}
