<?php

namespace Modules\Imonitor\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface EventRepository extends BaseRepository
{
    /**
     * @param $criteria
     * @param bool $params
     * @return mixed
     */
    public function getItem($criteria, $params = false);

    /**
     * @param $params
     * @return mixed
     */
    public function getItemsBy($params = false);

    /**
     * @param $id
     * @return mixed
     */
    public function whereProduct($id);

}
